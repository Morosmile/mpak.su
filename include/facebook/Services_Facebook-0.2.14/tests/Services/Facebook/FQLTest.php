<?php

require_once 'UnitTestCommon.php';

class Services_Facebook_FQLTest extends Services_Facebook_UnitTestCommon
{

    public function testQuery()
    {
        $response = <<<XML
<?xml version="1.0" encoding="UTF-8"?> <fql_query_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://api.facebook.com/1.0/ http://api.facebook.com/1.0/facebook.xsd" list="true">  <user>  <name>Mark Zuckerberg</name>  <affiliations list="true">  <affiliation>  <nid>50431648</nid>  <name>Facebook</name>  <type>work</type>  <status/>  <year/>  </affiliation>  <affiliation>  <nid>16777217</nid>  <name>Harvard</name>  <type>college</type>  <status>Undergrad</status>  <year/>  </affiliation>  </affiliations>  </user>  <user>  <name>Chris Hughes</name>  <affiliations list="true">  <affiliation>  <nid>50431648</nid>  <name>Facebook</name>  <type>work</type>  <status/>  <year/>  </affiliation>  </affiliations>  </user>  <user>  <name>Dustin Moskovitz</name>  <affiliations list="true">  <affiliation>  <nid>50431648</nid>  <name>Facebook</name>  <type>work</type>  <status/>  <year/>  </affiliation>  <affiliation>  <nid>16777217</nid>  <name>Harvard</name>  <type>college</type>  <status>Undergrad</status>  <year/>  </affiliation>  </affiliations>  </user> </fql_query_response>
XML;

        $this->mockSendRequest($response);
        $fql = "SELECT * FROM user WHERE uid=123";
        $result = $this->instance->query($fql);
        $this->assertType('SimpleXMLElement', $result);
    }

}

?>
