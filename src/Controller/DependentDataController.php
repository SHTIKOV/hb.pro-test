<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Transformer\EntityToIdTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DependentDataController extends AbstractController
{
    /**
     * @Route("/data", name="data")
     */
    public function index(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $data = [];
        $config = $this->getParameter('dependent_classes');
        $alias = $request->get('alias');
        $value = $request->get('value');

        if ($value && isset($config[$alias])) {
            $class = $config[$alias]['class'];
            $property = $config[$alias]['property'];
            /** @var EntityManagerInterface $em */
            $em = $this->container->get('doctrine.orm.entity_manager');
            $transformer = new EntityToIdTransformer($em, $class);

            foreach ($em->getRepository($class)->findBy([$property => $value]) as $item) {
                $data[$transformer->transform($item)] = (string) $item;
            }
        }

        return new JsonResponse($data);
    }
}
