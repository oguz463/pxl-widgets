<?php

namespace App\Parser;

class Parser
{
    /**
     * For extending parsing functionality
     *
     * @param string $data
     * @param ParserInterface $parser
     * @return ParserInterface
     */
    public static function parse($data, ParserInterface $parser)
    {
        return $parser->parse($data);
    }
}
