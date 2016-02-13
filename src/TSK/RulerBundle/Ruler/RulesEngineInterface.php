<?php
namespace TSK\RulerBundle\Ruler;

interface RulesEngineInterface
{
    public function buildContext($obj);
}
