<?php

namespace App\Tests\Controller\API;

use App\Tests\ApiTestCase;


class ProgrammerControllerTest extends ApiTestCase {
	protected function setUp(){
		parent::setUp();

		$this->createUser('weaverryan');
	}

	public function testPOSTProgrammer(){
		$data = array(
			'nickname' => 'ObjectOrienter',
			'avatarNumber' => 5,
			'tagLine' => '<?php'
		);

		//1) Post to create the programmer

		$response = $this->client->post('/api/programmers', [
			'body' => json_encode($data)
		]);

		$this->assertEquals(201,  $response->getStatusCode());
		$this->assertEquals('/api/programmers/ObjectOrienter',$response->getHeader('Location')[0]);
		$finishedData = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('nickname', $finishedData);
		$this->assertEquals('ObjectOrienter', $data['nickname']);
		$this->debugResponse($response);
	}

	public function testGetProgrammer(){
		$this->createProgrammer([
			'nickname' => 'UnitTester',
			'avatarNumber' => '3'
		]);

		$response = $this->client->get('/api/programmers/UnitTester');
		$this->assertEquals(200, $response->getStatusCode());
		$data = json_decode($response->getBody(), true);

		$this->asserter()->assertResponsePropertiesExist($response, [
			'nickname',
			'avatarNumber',
			'powerLevel',
			'tagLine'
		]);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'nickname',
			'UnitTester'
		);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'_links.self',
			'/api/programmers/UnitTester'
		);
	}

	public function testGetProgrammerDeep(){
		$this->createProgrammer([
			'nickname' => 'UnitTester',
			'avatarNumber' => '3'
		]);

		$response = $this->client->get('/api/programmers/UnitTester?deep=1');
		$this->assertEquals(200, $response->getStatusCode());
		$this->asserter()->assertResponsePropertyExists(
			$response,
			'user.username'
		);
	}

	public function testGETProgrammersCollection(){
		$this->createProgrammer([
			'nickname' => 'UnitTester',
			'avatarNumber' => '3'
		]);
		$this->createProgrammer([
			'nickname' => 'CowboyCoder',
			'avatarNumber' => '10'
		]);

		$response = $this->client->get('/api/programmers');
		$this->assertEquals(200, $response->getStatusCode());
		$this->asserter()->assertResponsePropertyIsArray($response, 'items');
		$this->asserter()->assertResponsePropertyCount($response, 'items', 2);
		$this->asserter()->assertResponsePropertyEquals($response, 'items[1].nickname', 'CowboyCoder');

	}

	public function testGETProgrammersCollectionPaginated(){
		$this->createProgrammer([
			'nickname' => 'willnotmatch',
			'avatarNumber' => 3
		]);

		for($i=0; $i<25; $i++){
			$this->createProgrammer([
				'nickname' => 'Programmer'. $i,
				'avatarNumber' => '3'
			]);
		}

		$response = $this->client->get('/api/programmers?filter=programmer');
		$this->assertEquals(200, $response->getStatusCode());

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'items[5].nickname',
			'Programmer5'
		);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'count',
			'10'
		);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'total',
			'25'
		);

		$this->asserter()->assertResponsePropertyExists(
			$response,
			'_links.next'
		);

		$nextUrl = $this->asserter()->readResponseProperty($response, '_links.next');

		$response = $this->client->get($nextUrl);
		$this->assertEquals(200, $response->getStatusCode());

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'items[5].nickname',
			'Programmer15'
		);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'count',
			'10'
		);

		$this->asserter()->assertResponsePropertyExists(
			$response,
			'_links.next'
		);

		$lastUrl = $this->asserter()->readResponseProperty($response, '_links.last');
		$response = $this->client->get($lastUrl);
		$this->assertEquals(200, $response->getStatusCode());

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'items[4].nickname',
			'Programmer24'
		);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'count',
			'5'
		);

		$this->asserter()->assertResponsePropertyDoesNotExist(
			$response,
			'programmers[5].nickname'
		);
	}

	public function testPUTProgrammer(){
		$data = array(
			'nickname' => 'CowgirlCoder',
			'avatarNumber' => 2,
			'tagLine' => 'foo'
		);

		$this->createProgrammer([
			'nickname' => 'CowboyCoder',
			'avatarNumber' => '10'
		]);

		$response = $this->client->put('/api/programmers/CowboyCoder', [
			'body' => json_encode($data)
		]);
		$this->assertEquals(200, $response->getStatusCode());
		$data = json_decode($response->getBody(), true);

		$this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 2);
		$this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'CowboyCoder');
	}

	public function testDELETEProgrammer(){
		$this->createProgrammer([
			'nickname' => 'UnitTester',
			'avatarNumber' => '3'
		]);

		$response = $this->client->delete('/api/programmers/UnitTester');
		$this->assertEquals(204, $response->getStatusCode());
	}

	public function testPATCHProgrammer(){
		$data = array(
			'tagLine' => 'bar'
		);

		$this->createProgrammer([
			'nickname' => 'CowboyCoder',
			'avatarNumber' => '10'
		]);

		$response = $this->client->patch('/api/programmers/CowboyCoder', [
			'body' => json_encode($data)
		]);
		$this->assertEquals(200, $response->getStatusCode());
		$data = json_decode($response->getBody(), true);

		$this->asserter()->assertResponsePropertyEquals($response, 'tagLine', 'bar');
		$this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 10);
	}

	public function testValiationErrors(){
		$data = array(
			'avatarNumber' => 5,
			'tagLine' => '<?php'
		);

		//1) Post to create the programmer

		$response = $this->client->post('/api/programmers', [
			'body' => json_encode($data)
		]);

		$this->assertEquals(400,  $response->getStatusCode());
		$this->asserter()->assertResponsePropertiesExist($response, [
			'type',
			'title',
			'errors'
		]);
		$this->asserter()->assertResponsePropertyExists($response, 'errors.nickname');
		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'errors.nickname[0]',
			'Please enter a clever nickname!'
		);
		$this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.avatarNumber');
		$this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
	}

	public function testInvalidJson(){
		$invalidJson = <<<EOF
{
    "nickname": "JohnnyRobot",
    "avatarNumber" : "2
    "tagLine": "I'm from a test!"
}
EOF;
		//1) Post to create the programmer

		$response = $this->client->post('/api/programmers', [
			'body' => $invalidJson
		]);

		$this->assertEquals(400,  $response->getStatusCode());
		$this->asserter()->assertResponsePropertyContains(
			$response,
			'type',
			'invalid_body_format'
		);

		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'title',
			'Invalid JSON format sent'
		);
	}

	public function test404Exception(){
		$response = $this->client->get('/api/programmers/fake');

		$this->assertEquals(404,  $response->getStatusCode());
		$this->assertEquals(
			'application/problem+json',
			$response->getHeader('Content-Type')[0]
		);
		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'type',
			'about:blank'
		);
		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'title',
			'Not Found'
		);
		$this->asserter()->assertResponsePropertyEquals(
			$response,
			'detail',
			'No programmer found for username fake'
		);
	}


}
