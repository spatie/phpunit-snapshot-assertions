<?php

namespace Spatie\Snapshots\Test;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function can_match_a_string_snapshot()
    {
        $data = 'Foo';

        $this->assertMatchesSnapshot($data);
    }

    /** @test */
    public function can_match_an_xml_screenshot()
    {
        $data = '<foo><bar>Baz</bar></foo>';

        $this->assertMatchesXmlSnapshot($data);
    }

    /** @test */
    public function can_match_a_json_screenshot()
    {
        $data = '{"foo":"foo","bar":"bar","baz":"baz"}';

        $this->assertMatchesJsonSnapshot($data);
    }
}
