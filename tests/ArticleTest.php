<?php
namespace App\Tests\Entity;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use App\Tests\Entity\Tools\AssertEntityTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
class ArticleTest extends KernelTestCase
{
    use AssertEntityTrait; 
    public function setUp(): void
    {
        $this->cat= (new Category())
            ->setName("CatTest");
        $this->user = (new User())
            ->setFirstName("John")
            ->setLastName("Smith")
            ->setEmail("john.smith@gmail.com")
            ->setIsVerified(true)
            ->setPassword("azertyuiop");
        $this->article = (new Article())
            ->setName("Article Test")
            ->setDescription("Lorem ipsum dolor sit amet consectetur adipisicing elit Lorem ipsum dolor sit amet consectetur adipisicing elit")
            ->setPrice(10.50)
            ->setState("wait-review")
            ->setAuthor($this->user)
            ->setCategory($this->cat);
    }
    public function testValideArticle(): void
    {
        //$errors = $validator->validate($article);
        //$this->assertCount(0, $errors/*, $errors[0]->getMessage()*/);
        $this->assertHasErrors($this->article, 0);
    }
    public function testNameBlankArticle(): void {
        $this->assertHasErrors($this->article->setName(""), 2, 'name', 'This value should not be blank.');
        $this->assertHasErrors($this->article->setName(""), 2, 'name', 'This value is too short. It should have 2 characters or more.');
    }
    public function testNameTooLongArticle(): void {
        $this->assertHasErrors($this->article->setName("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"), 1, 'name', 'This value is too long. It should have 100 characters or less.');
        //$this->assertHasErrors($this->article->setName("aaaaaaaaaa"), 1, 'name', 'This value is not valid.');
    }
    public function testNameTooShortArticle(): void {
        $this->assertHasErrors($this->article->setName("a"), 1, 'name', 'This value is too short. It should have 2 characters or more.');
    }
    public function testNameNotValideArticle(): void {
        $this->assertHasErrors($this->article->setName(">>"), 1, 'name', 'This value is not valid.');
    }
    public function testDescriptionBlankArticle(): void {
        $this->assertHasErrors($this->article->setDescription(""), 2, 'description', 'This value should not be blank.');
        // $this->assertHasErrors($this->article->setDescription(""), 2, 'description', 'This value is too short. It should have 2 characters or more.');
    }
    public function testDescriptionTooShortArticle(): void {
        $this->assertHasErrors($this->article->setDescription("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"), 1, 'description', 'This value is too short. It should have 50 characters or more.');
    }
    public function testDescriptionNotValideArticle(): void {
        $this->assertHasErrors($this->article->setDescription("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa>>"), 1, 'description', 'This value is not valid.');
    }

    
}


