<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerTest extends WebTestCase
{

    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function loginUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['username' => 'carlu', 'password' => 'password']);
        $this->client->catchExceptions(false);
    }

    public function testListAction()
    {
        $this->loginUser();
        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateAction()
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = uniqid().'autre';
        $form['user[password][first]'] = 'autre';
        $form['user[password][second]'] = 'autre';
        $form['user[email]'] = uniqid().'@autre.org';
        $form['user[roles]'] = "ROLE_USER";
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testUpdateAction()
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/users/7/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'nouveau';
        $form['user[password][first]'] = 'nouveau';
        $form['user[password][second]'] = 'nouveau';
        $form['user[email]'] = 'nouveau@nouveau.org';
        $form['user[roles]'] = "ROLE_USER";
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
}