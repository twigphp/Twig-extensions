<?php

class Twig_Extensions_IndentLexer extends Twig_Lexer
{
    public function __construct(Twig_Environment $env, array $options = array())
    {
        parent::__construct($env, $options);

        $this->regexes['lex_block'] = str_replace('\\n?', '', $this->regexes['lex_block']);
        $this->regexes['lex_comment'] = str_replace('\\n?', '', $this->regexes['lex_comment']);
    }

    public function tokenize($code, $filename = null)
    {
        parent::tokenize($code, $filename);

        // fetch indent extension parameters

        $indent = $this->env->getExtension('indent');
        $indent_str = $indent->getIndentString();
        $indent_len = strlen($indent_str);
        $start_tags = $indent->getStartTags();
        $end_tags = $indent->getEndTags();

        // find block pairs {% %} and {{ }}

        $pairs = array();
        $search_type = null;

        for ($i = 0, $n = count($this->tokens); $i < $n; $i++)
        {
            $type = $this->tokens[$i]->getType();

            if ($type === $search_type)
            {
                $pairs[] = $i;
                $search_type = null;
            }
            else if ($type === Twig_Token::BLOCK_START_TYPE)
            {
                $pairs[] = $i;
                $search_type = Twig_Token::BLOCK_END_TYPE;
            }
            else if ($type === Twig_Token::VAR_START_TYPE)
            {
                $pairs[] = $i;
                $search_type = Twig_Token::VAR_END_TYPE;
            }
        }

        // find single line pairs, fill $substr for text token modifications, and fill $insert
        // for indent/unindent filter injection

        $insert = array();
        $substr = array();
        $nsubstr = 0;
        $texttype = Twig_Token::TEXT_TYPE;
        $texttoken = new Twig_Token($texttype, "\n", 0);

        $INDENT = 0;
        $UNINDENT = 1;
        $START = 0;
        $END = 1;

        for ($i = 0, $n = count($pairs); $i < $n; $i += 2)
        {
            $beg = $pairs[$i + 0];
            $end = $pairs[$i + 1];

            $iprev = $beg - 1;
            $inext = $end + 1;

            $prev = $iprev >= 0 ? $this->tokens[$iprev] : $texttoken;
            $next = $this->tokens[$inext];

            if ($prev->getType() === $texttype && $next->getType() === $texttype)
            {
                $prev_value = $prev->getValue();
                $next_value = $next->getValue();

                $nl = strrpos($prev_value, "\n");

                if ($nl === false && $iprev === 0)
                    $nl = -1;

                if ($nl !== false && $next_value[0] === "\n")
                {
                    $count = strlen($prev_value) - $nl - 1;

                    if (strspn($prev_value, " \t", $nl + 1) === $count)
                    {
                        // the pair is in single line

                        if ($this->tokens[$beg]->getType() === Twig_Token::BLOCK_START_TYPE)
                        {
                            // substr

                            if ($nsubstr > 0 && $substr[$nsubstr - 3] === $iprev)
                            {
                                $substr[$nsubstr - 1] = $count;
                            }
                            else if ($iprev >= 0)
                            {
                                $substr[] = $iprev;
                                $substr[] = 0;
                                $substr[] = $count;

                                $nsubstr += 3;
                            }

                            $substr[] = $inext;
                            $substr[] = 1;
                            $substr[] = 0;

                            $nsubstr += 3;

                            // unindent injection

                            $tag = $this->tokens[$beg + 1]->getValue();

                            if (in_array($tag, $end_tags))
                            {
                                $insert[] = $beg;
                                $insert[] = $END;
                                $insert[] = $UNINDENT;
                            }

                            if (in_array($tag, $start_tags))
                            {
                                $insert[] = $end + 1;
                                $insert[] = $START;
                                $insert[] = $UNINDENT;
                            }
                        }
                        else
                        {
                            // indent injection

                            $nindent = (int)($count / $indent_len);

                            if ($nindent > 0)
                            {
                                $insert[] = $beg;
                                $insert[] = $START;
                                $insert[] = $INDENT;
                                $insert[] = $nindent;

                                $insert[] = $end + 1;
                                $insert[] = $END;
                                $insert[] = $INDENT;
                            }
                        }
                    }
                }
            }
        }

        // consume $substr

        for ($i = 0; $i < $nsubstr; $i += 3)
        {
            $index = $substr[$i];
            $start = $substr[$i + 1];
            $length = $substr[$i + 2];
            $token = $this->tokens[$index];
            $value = $token->getValue();

            if ($length > 0)
                $value = substr($value, $start, -$length);
            else
                $value = substr($value, $start);

            if ($value !== '')
                $this->tokens[$index] = new Twig_Token($texttype, $value, $token->getLine());
            else
                $this->tokens[$index] = null;
        }

        // fill the final $tokens array while consuming $insert

        $tokens = array();
        $t = 0;

        for ($i = 0, $n = count($insert); $i < $n; $i += 3)
        {
            $index = $insert[$i];
            $type = $insert[$i + 1];
            $filter_func = $insert[$i + 2];

            while ($t < $index)
            {
                $token = $this->tokens[$t++];

                if ($token !== null)
                    $tokens[] = $token;
            }

            if ($type === $START)
            {
                $line = $this->tokens[$t + ($filter_func === $INDENT ? 0 : -1)]->getLine();
                $func = ($filter_func === $INDENT ? 'indent' : 'unindent');
                $count = 1;

                if ($filter_func === $INDENT)
                    $count = $insert[($i++) + 3];

                array_push(
                    $tokens,
                    new Twig_Token(Twig_Token::BLOCK_START_TYPE, '', $line),
                    new Twig_Token(Twig_Token::NAME_TYPE, 'filter', $line),
                    new Twig_Token(Twig_Token::NAME_TYPE, $func, $line),
                    new Twig_Token(Twig_Token::PUNCTUATION_TYPE, '(', $line),
                    new Twig_Token(Twig_Token::NUMBER_TYPE, $count, $line),
                    new Twig_Token(Twig_Token::PUNCTUATION_TYPE, ')', $line),
                    new Twig_Token(Twig_Token::BLOCK_END_TYPE, '', $line)
                );
            }
            else
            {
                $line = $this->tokens[$t + ($filter_func === $INDENT ? -1 : 0)]->getLine();

                $tokens[] = new Twig_Token(Twig_Token::BLOCK_START_TYPE, '', $line);
                $tokens[] = new Twig_Token(Twig_Token::NAME_TYPE, 'endfilter', $line);
                $tokens[] = new Twig_Token(Twig_Token::BLOCK_END_TYPE, '', $line);
            }
        }

        $n = count($this->tokens);

        while ($t < $n)
        {
            $token = $this->tokens[$t++];

            if ($token !== null)
                $tokens[] = $token;
        }

        $this->tokens = $tokens;

        return new Twig_TokenStream($this->tokens, $this->filename);
    }
}
