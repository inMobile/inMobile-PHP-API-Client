<?php

namespace Inmobile\InmobileSDK\Traits;

trait HasPagination
{
    public function fetchAllFrom(string $url): array
    {
        $entries = [];
        $result = $this->api->get($url, ['pageLimit' => 250]);

        if (!$result->toObject()->_links->isLastPage) {
            $entries = $this->fetchAllFrom(
                str_replace('v4/', '', $result->toObject()->_links->next)
            );
        }

        return array_merge($entries, $result->toObject()->entries);
    }
}
