<?php

namespace Cyvelnet\Laravel5Fractal\Serializers;

use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;

/**
 * Class ExtendedArraySerializer.
 */
class ExtendedArraySerializer extends ArraySerializer
{
    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int) $paginator->getCurrentPage();
        $lastPage = (int) $paginator->getLastPage();

        $pagination = [
            'total'        => (int) $paginator->getTotal(),
            'count'        => (int) $paginator->getCount(),
            'per_page'     => (int) $paginator->getPerPage(),
            'current_page' => $currentPage,
            'total_pages'  => $lastPage,
        ];

        $pagination['links'] = [];

        if ($currentPage !== 1) {
            $pagination['links']['first'] = $paginator->getUrl(1);
        }

        if ($currentPage > 1) {
            $pagination['links']['previous'] = $paginator->getUrl($currentPage - 1);
        }

        // add fast backward
        if (($fastBack = ($currentPage - 10)) >= 1) {
            $pagination['links']['fastback'] = $paginator->getUrl($fastBack);
        }

        if ($currentPage < $lastPage) {
            $pagination['links']['next'] = $paginator->getUrl($currentPage + 1);
        }

        // add fast backward
        if (($fastForward = ($currentPage + 10)) <= $lastPage) {
            $pagination['links']['fastforward'] = $paginator->getUrl($fastForward);
        }

        if ($lastPage > 1 && $currentPage !== $lastPage) {
            $pagination['links']['last'] = $paginator->getUrl($lastPage);
        }

        return ['pagination' => $pagination];
    }
}
