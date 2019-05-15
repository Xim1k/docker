<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        //add Admin
        $user = new User();
        $user->setName('admin');
        $user->setSurname('ADMIN');
        $user->setPassword($this->encoder->encodePassword(
            $user,
            '12345678'
        ));
        $user->setEmail('ema@ema.ru');
        $user->setLogin('sup_admin');
        $user->setRoles(User::ROLE_ADMIN);
        $manager->persist($user);
        $manager->flush();
       // $user = $manager->getRepository(User::class)->find(1);






        //add Category
        $category = new Category();
        $category->setName('Мясо');
        $manager->persist($category);
        $category = new Category();
        $category->setName('Фрукты и овощи');
        $manager->persist($category);
        $category = new Category();
        $category->setName('Напитки');
        $manager->persist($category);
        $category = new Category();
        $category->setName('Молочные продукты');
        $manager->persist($category);

        //add Saller
        $saller = new Seller();
        $saller->setName('Мясной магазин');
        $saller->setDescription('Всегда только свежее мясо.');
        $saller->setAddress('Россия, Новосибирск, Вокзальная магистраль, 16 ');
        $manager->persist($saller);


        $user = new User();
        $user->setName('Иван');
        $user->setSurname('Петров');
        $user->setPassword($this->encoder->encodePassword(
            $user,
            'dev12345'
        ));
        $user->setEmail('dgs@dfger.ru');
        $user->setLogin('saller');
        $user->setSeller($saller);
        $manager->persist($user);
        $user->setRoles(User::ROLE_SELLER_MAIN);
        $manager->flush();

        $manager->persist($saller);

        $saller = new Seller();
        $saller->setName('Молочный магазин');
        $saller->setDescription('Фермерское молоко в Новосибирске.');
        $saller->setAddress('Россия, Новосибирск, Красный проспект, 17');
        $manager->persist($saller);

        $user = new User();
        $user->setName('Василий');
        $user->setSurname('Абрамов');
        $user->setPassword($this->encoder->encodePassword(
            $user,
            'dev12345'
        ));
        $user->setEmail('dhh@rgsgr.ru');
        $user->setLogin('saller1');
        $user->setSeller($saller);
        $manager->persist($user);
        $user->setRoles(User::ROLE_SELLER_MAIN);
        $manager->flush();
        $user = $manager->getRepository(User::class)->find(1);

        $manager->persist($saller);

        $saller = new Seller();
        $saller->setName('Магазин напитков');
        $saller->setDescription('Лучший лимонад на свете.');
        $saller->setAddress('Россия, Новосибирск, Гурьевская улица, 51');
        $manager->persist($saller);

        $user = new User();
        $user->setName('Александ');
        $user->setSurname('Веселов');
        $user->setPassword($this->encoder->encodePassword(
            $user,
            'dev12345'
        ));
        $user->setEmail('ndhthnd@gsrehdth.ru');
        $user->setLogin('saller2');
        $user->setSeller($saller);
        $manager->persist($user);
        $user->setRoles(User::ROLE_SELLER_MAIN);
        $manager->flush();

        $manager->persist($saller);

        $saller = new Seller();
        $saller->setName('Фрукты и овощи');
        $saller->setDescription('Свежий фрукт.');
        $saller->setAddress('Россия, Новосибирск, улица Блюхера, 32 ');
        $manager->persist($saller);
        $manager->flush();

        $user = new User();
        $user->setName('Анна');
        $user->setSurname('Иванова');
        $user->setPassword($this->encoder->encodePassword(
            $user,
            'dev12345'
        ));
        $user->setEmail('ftjtyj@htddrth.ru');
        $user->setLogin('saller3');
        $user->setSeller($saller);
        $manager->persist($user);
        $user->setRoles(User::ROLE_SELLER_MAIN);
        $manager->flush();


        $manager->persist($saller);

        //add Products

        $categoryRepository = $manager->getRepository(Category::class);
        $sellerRepository = $manager->getRepository(Seller::class);
        //add milk products
        $category = $categoryRepository->findOneBy(['name' => 'Молочные продукты']);
        $seller = $sellerRepository->findOneBy(['name' => 'Молочный магазин']);
        $product = new Product();
        $product->setName('Молоко');
        $product->setCategory($category);
        $product->setCount(10);
        $product->setDescription('Вкусное и свежее молоко с проверенных ферм, 100% натуральное');
        $product->setPrice(50);
        $product->setExternalId(1);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Сыр');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Натуральный сыр  не содержит красителей, зато богат кальцием и обладает выверенной консистенцией.');
        $product->setPrice(550);
        $product->setExternalId(2);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Сливки');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Сливки - это продукт, созданный из 100% натурального коровьего молока.');
        $product->setPrice(80);
        $product->setExternalId(3);
        $product->setSeller($seller);
        $manager->persist($product);

        //add Напитки
        $category = $categoryRepository->findOneBy(['name' => 'Напитки']);
        $seller = $sellerRepository->findOneBy(['name' => 'Магазин напитков']);
        $product = new Product();
        $product->setName('Вода');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Кристальная вода чистейших источников Алтая.');
        $product->setPrice(35);
        $product->setExternalId(4);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Лимонад');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Классические лимонады на основе артезианской воды');
        $product->setPrice(65);
        $product->setExternalId(5);
        $product->setSeller($seller);
        $manager->persist($product);

        //add fruit and vegetables
        $category = $categoryRepository->findOneBy(['name' => 'Фрукты и овощи']);
        $seller = $sellerRepository->findOneBy(['name' => 'Фрукты и овощи']);
        $product = new Product();
        $product->setName('Яблоки');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Обладают очень сладким и гармоничным вкусом и великолепным ароматом.');
        $product->setPrice(96);
        $product->setExternalId(6);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Бананы');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Бананы являются одной из древнейших пищевых культур..');
        $product->setPrice(58);
        $product->setExternalId(7);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Томат');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Имеют отличный вкус и способствуют укреплению здоровья.');
        $product->setPrice(220);
        $product->setExternalId(8);
        $product->setSeller($seller);
        $manager->persist($product);

        $category = $categoryRepository->findOneBy(['name' => 'Мясо']);
        $seller = $sellerRepository->findOneBy(['name' => 'Мясной магазин']);

        $product = new Product();
        $product->setName('Курица');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Нежное куриное филе подходит для приготовления супов, салатов.');
        $product->setPrice(190);
        $product->setExternalId(9);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Говядина');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Исключительно свежее мясо, которое полностью отвечает требованиям «халяль».');
        $product->setPrice(550);
        $product->setExternalId(10);
        $product->setSeller($seller);
        $manager->persist($product);

        $product = new Product();
        $product->setName('Свинина');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Идеальный вариант для супов, гриля, домашних копченостей и закусок.');
        $product->setPrice(270);
        $product->setExternalId(11);
        $product->setSeller($seller);
        $manager->persist($product);



        $manager->flush();


    }
}
