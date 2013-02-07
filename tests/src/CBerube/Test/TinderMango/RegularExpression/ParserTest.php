<?php

use CBerube\TinderMango\RegularExpression\Parser;
use CBerube\TinderMango\RegularExpression\PatternNode\Alternation;
use CBerube\TinderMango\RegularExpression\PatternNode\CapturingGroup;
use CBerube\TinderMango\RegularExpression\PatternNode\Literal;

class ParserTest extends PHPUnit_Framework_Testcase
{
    /** @var \CBerube\TinderMango\RegularExpression\Parser */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testLiteralPattern()
    {
        $testRegex = '~I am a literal regular expression~';

        $pattern = $this->parser->parse($testRegex);

        $nodeList = $pattern->getNodeList();

        $this->assertCount(1, $nodeList);
        $this->assertPatternNodeType('Literal', $nodeList[0]);
        $this->assertEquals("I am a literal regular expression", strval($nodeList[0]));
    }

    /**
     * @param $regularExpression
     * @param $expectedAlternativeList
     *
     * @dataProvider topLevelAlternationDAtaProvider
     */
    public function testTopLevelAlternation($regularExpression, $expectedAlternativeList)
    {
        $expectedAlternativeCount = count($expectedAlternativeList);

        $pattern = $this->parser->parse($regularExpression);

        $nodeList = $pattern->getNodeList();

        $this->assertCount(1, $nodeList);
        $this->assertPatternNodeType('Alternation', $nodeList[0]);

        /** @var $alternation \CBerube\TinderMango\RegularExpression\PatternNode\Alternation */
        $alternation = $nodeList[0];
        $alternativeList = $alternation->getAlternativeList();

        $this->assertCount($expectedAlternativeCount, $alternativeList);
        $this->assertContainsOnlyInstancesOf($this->getClassNameForPatternNode('Literal'), $alternativeList);

        for ($i = 0; $i < $expectedAlternativeCount; $i++) {
            $this->assertEquals($expectedAlternativeList[$i], strval($alternativeList[$i]));
        }
    }

    public function testLiteralCapturingGroup()
    {
        $regularExpression = '~I expect to always (capture) the same value~';

        $expectedCapturingGroup = new CapturingGroup();
        $expectedCapturingGroup->addNode(new Literal('capture'));

        $expectedNodes = array(
            new Literal('I expect to always '),
            $expectedCapturingGroup,
            new Literal(' the same value')
        );

        $pattern = $this->parser->parse($regularExpression);

        $this->assertEquals($expectedNodes, $pattern->getNodeList());
    }

    public function testAlternationCapturingGroup()
    {
        $regularExpression = '~I can (capture|collect) two different values~';

        $expectedAlternation = new Alternation();
        $expectedAlternation->addNode(new Literal('capture'));
        $expectedAlternation->addNode(new Literal('collect'));

        $expectedCapturingGroup = new CapturingGroup();
        $expectedCapturingGroup->addNode($expectedAlternation);

        $expectedNodes = array(
            new Literal('I can '),
            $expectedCapturingGroup,
            new Literal(' two different values')
        );

        $pattern = $this->parser->parse($regularExpression);

        $this->assertEquals($expectedNodes, $pattern->getNodeList());
    }

    public function topLevelAlternationDataProvider()
    {
        return array(
            array(
                "/All the king's horses|All the king's men/",
                array("All the king's horses", "All the king's men")
            )
        );
    }

    private function getClassNameForPatternNode($type)
    {
        return "CBerube\\TinderMango\\RegularExpression\\PatternNode\\$type";
    }

    private function assertPatternNodeType($expectedType, $object)
    {
        $this->assertInstanceOf(
            $this->getClassNameForPatternNode($expectedType),
            $object
        );
    }
}
