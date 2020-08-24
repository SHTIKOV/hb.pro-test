<?php

namespace App\Service;

use App\Entity\SearchRequest;
use Doctrine\ORM\EntityManagerInterface;

class HotelSearch
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var HotelRetrieverInterface
     */
    private $retriever;

    /**
     * HotelSearch constructor.
     * @param EntityManagerInterface $em
     * @param HotelRetrieverInterface $retriever
     */
    public function __construct(EntityManagerInterface $em, HotelRetrieverInterface $retriever)
    {
        $this->em = $em;
        $this->retriever = $retriever;
    }

    /**
     * @param SearchRequest $request
     * @return boolean
     * @throws \Exception
     */
    public function search(SearchRequest $request): bool
    {
        try {
            $this->em->persist($request);
            $results = $this->retriever->getByRequest($request);
            array_map([$this->em, 'persist'], $results);
            $request->setStatus(SearchRequest::STATUS_COMPLETE);
        } catch (\Exception $exception) {
            $request->setStatus(SearchRequest::STATUS_ERROR);
            throw $exception;
        } finally {
            $this->em->flush();
        }

        return $request->getStatus() === SearchRequest::STATUS_COMPLETE;
    }
}