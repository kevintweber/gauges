<?php

namespace Kevintweber\Gauges\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Kevintweber\Gauges\Factory;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->me();
        $this->assertInstanceOf('GuzzleHttp\Message\Response', $response);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/me");
    }

    public function testUpdateMe()
    {
        $request = $this->buildRequest(200);
        $response = $request->update_me('Kevin', 'Weber');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/me?first_name=Kevin&last_name=Weber");

        $response = $request->update_me(null, 'Weber');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/me?last_name=Weber");
    }

    public function testListClients()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_clients();
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients");
    }

    public function testCreateClient()
    {
        $request = $this->buildRequest(200);
        $response = $request->create_client();
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients");

        $response = $request->create_client('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients?description=asdf");
    }

    public function testDeleteClients()
    {
        $request = $this->buildRequest(200);
        $response = $request->delete_client('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/clients/asdf");
    }

    public function testListGauges()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_gauges();
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges");

        $response = $request->list_gauges(3);
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges?page=3");
    }

    public function testCreateGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->create_gauge('asdf', 'America/New_York');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges?title=asdf&tz=America%2FNew_York");

        /// @todo
    }

    public function testGaugeDetail()
    {
        $request = $this->buildRequest(200);
        $response = $request->gauge_detail('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf");
    }

    public function testUpdateGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->update_gauge('asdf1', 'asdf2', 'America/New_York');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf1?title=asdf2&tz=America%2FNew_York");

        /// @todo
    }

    public function testListShares()
    {
        $request = $this->buildRequest(200);
        $response = $request->list_shares('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/shares");
    }

    public function testShareGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->share_gauge('asdf', 'kevintweber@gmail.com');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/shares?email=kevintweber%40gmail.com");
    }

    public function testUnshareGauge()
    {
        $request = $this->buildRequest(200);
        $response = $request->unshare_gauge('asdf', 'kevintweber');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/shares/kevintweber");
    }

    public function testTopContent()
    {
        $request = $this->buildRequest(200);
        $response = $request->top_content('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/content");

        /// @todo
    }

    public function testTopReferrers()
    {
        $request = $this->buildRequest(200);
        $response = $request->top_referrers('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/referrers");

        /// @todo
    }

    public function testTraffic()
    {
        $request = $this->buildRequest(200);
        $response = $request->traffic('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/traffic");

        /// @todo
    }

    public function testBrowserResolutions()
    {
        $request = $this->buildRequest(200);
        $response = $request->browser_resolutions('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/resolutions");

        /// @todo
    }

    public function testTechnology()
    {
        $request = $this->buildRequest(200);
        $response = $request->technology('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/technology");

        /// @todo
    }

    public function testSearchTerms()
    {
        $request = $this->buildRequest(200);
        $response = $request->search_terms('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/terms");

        /// @todo
    }

    public function testSearchEngines()
    {
        $request = $this->buildRequest(200);
        $response = $request->search_engines('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/engines");

        /// @todo
    }

    public function testLocations()
    {
        $request = $this->buildRequest(200);
        $response = $request->locations('asdf');
        $this->assertEquals($response->getEffectiveUrl(),
                            "https://secure.gaug.es/gauges/asdf/locations");

        /// @todo
    }

    /**
     * Helper function for testing requests.
     *
     * @param int   $statusCode
     * @param mixed $body
     *
     * @return Request
     */
    protected function buildRequest($statusCode, $body = null)
    {
        if ($body === null) {
            $body = '{"test":"fake"}';
        }

        $responseBody = $body;
        if (is_array($body)) {
            $responseBody = json_encode($body);
        }

        $response = new Response($statusCode, array(),
                                 Stream::factory($responseBody));

        return Factory::getMockingRequest($response);
    }
}