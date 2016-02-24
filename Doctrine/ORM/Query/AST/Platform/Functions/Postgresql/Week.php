<?php

namespace UCI\Boson\BackendBundle\Doctrine\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use UCI\Boson\BackendBundle\Doctrine\ORM\Query\AST\Functions\SimpleFunction;

class Week extends AbstractTimestampAwarePlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $expression */
        $expression = $this->parameters[SimpleFunction::PARAMETER_KEY];
        return 'EXTRACT(WEEK FROM ' . $this->getTimestampValue($expression, $sqlWalker) . ')';
    }
}
