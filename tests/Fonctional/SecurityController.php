<?php

namespace App\Tests\Fonctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityController extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('form > h1', 'Hello World');

        $form =  $crawler->filter('button[type=submit]')->form();
        $form['email'] = 'admin@yopmail.com';
        $form['password'] = 'azertyuiop';
        $crawler = $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('header > div > a > span', 'CW2 Site');
        $this->assertSelectorTextContains('nav > a:nth-child(4)', 'Logout');

        $crawler = $client->getCrawler();//Un peu comme le dom
        $linkLogout    = $crawler->filter('a:contains("Logout")')->eq(0)->link();
        $crawler = $client->click($linkLogout);
       

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('header > div > a > span', 'CW2 Site');
        $this->assertSelectorTextContains('nav > a:nth-child(2)', 'login');
    }

    public function testRegister(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('form > h1', 'Hello World');

        $form =  $crawler->filter('button[type=submit]')->form();
        $form['registration_form[firstName]'] = 'test';
        $form['registration_form[lastName]'] = 'test';
        $form['registration_form[email]'] = 'test'.time().'@yopmail.com';
        $form['registration_form[plainPassword][first]'] = 'azertyuiop';
        $form['registration_form[plainPassword][second]'] = 'azertyuiop';
        $form['registration_form[agreeTerms]'] = 1;
        $crawler = $client->submit($form);
      
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHeaderSame($email, 'subject', 'Please Confirm your Email');

        // $client->followRedirect();
        // $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('h1', 'Please sign in');

        preg_match('/(\/verify\/email\?[^"]*)/', $email->getHtmlBody(), $url);
        $linkValidation = html_entity_decode($url[0]);


        $crawler = $client->request('GET', $linkValidation);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');

    }
}
