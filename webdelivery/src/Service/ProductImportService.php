<?php
/**
 * Created by PhpStorm.
 * User: diman
 * Date: 31.03.19
 * Time: 10:21
 */

namespace App\Service;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\EntityManager;



class ProductImportService
{
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function importCsv($file,?User $user)
    {
        $originalFileName = $file->getClientOriginalName();
        $fileRealPAth = $file->getRealPath();
        //import products from csv and xml

        $arr_file = explode('.', $originalFileName);
        $extension = end($arr_file);
        if('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } elseif ('xml' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
        }
        $spreadsheet = $reader->load($fileRealPAth);
        $importProducts = $spreadsheet->getActiveSheet()->toArray();
        $this->pushProductToBase($importProducts, $user);
        return true;

    }
    private function pushProductToBase($importProducts, ?User $user){
        $productRepository = $this->manager->getRepository(Product::class);
        $categoryRepository = $this->manager->getRepository(Category::class);

        foreach ($importProducts as $key => $importProduct)
        {
            $importProductId=$importProduct[0];
            $product = $productRepository->findOneBy(['external_id'=>$importProductId]);
            $category = $categoryRepository->findOneBy(['name' => $importProduct[7]]);

            if (!$category) {
                continue;
            }

            if (!empty($product)) {
                $product->setName($importProduct[3]);
                $product->setDescription($importProduct[4]);
                $product->setCount($importProduct[5]);
                $product->setPrice($importProduct[6]);
                $product->setCategory($category);
                $product->setSeller($user->getSeller());

            } else {
                $product = new Product();
                $product->setName($importProduct[3]);
                $product->setDescription($importProduct[4]);
                $product->setCount($importProduct[5]);
                $product->setPrice($importProduct[6]);
                $product->setCategory($category);
                $product->setExternalId($importProduct[0]);
                $product->setSeller($user->getSeller());
            }
            $this->manager->persist($product);
            $this->manager->flush();
        }
        return true;
    }
}