<?php

namespace MailerPress\Services;

use MailerPress\Core\Interfaces\ContactFetcherInterface;
use WP_REST_Request;
use WP_Error;

class SegmentContactFetcher implements ContactFetcherInterface
{
    private string $segmentName;

    /**
     * @param string $segmentName The human-readable segment "name" used by the endpoint.
     */
    public function __construct(string $segmentName)
    {
        $this->segmentName = $segmentName;
    }

    /**
     * Fetch contacts (IDs) from the segment REST endpoint using internal dispatch.
     *
     * @param int $limit Max number of contacts requested.
     * @param int $offset Starting offset (0-based).
     * @return array Array of contact IDs (int) OR full objects if endpoint misconfigured (we coerce where possible).
     */
    public function fetch(int $limit, int $offset): array
    {
        // Safety
        if ($limit <= 0) {
            return [];
        }

        // Map offset/limit to page/per_page for the REST API.
        // page is 1-based in WP REST.
        $page = (int)floor($offset / $limit) + 1;
        $per_page = $limit;

        $request = new WP_REST_Request('GET', '/mailerpress/v1/getContactSegment');
        $request->set_param('segmentName', $this->segmentName);
        $request->set_param('onlyIds', true);
        $request->set_param('page', $page);
        $request->set_param('per_page', $per_page);
        $request->set_param('_internal_key', wp_hash('mailerpress-internal'));

        $response = rest_do_request($request);

        if ($response->is_error()) {
            /** @var WP_Error $error */
            return [];
        }

        $status = $response->get_status();
        if ($status < 200 || $status >= 300) {
            return [];
        }

        $data = $response->get_data();
        if (empty($data)) {
            return [];
        }

        // Normalize: we expect an array of rows. If $onlyIds==true, each row may be an object w/ contact_id property
        // or a scalar (depending on how the endpoint is implemented / DB results fetched).
        $ids = [];
        foreach ($data as $row) {
            if (is_object($row) && isset($row->contact_id)) {
                $ids[] = (int)$row->contact_id;
            } elseif (is_array($row) && isset($row['contact_id'])) {
                $ids[] = (int)$row['contact_id'];
            } elseif (is_scalar($row)) {
                // Fallback: assume raw ID
                $ids[] = (int)$row;
            }
        }

        // Defensive slice in case the endpoint returned extra rows (shouldn’t happen if paging works).
        // Where $offsetWithinPage = $offset % $limit.
        $offsetWithinPage = $offset % $limit;
        if ($offsetWithinPage > 0 || count($ids) > $limit) {
            $ids = array_slice($ids, $offsetWithinPage, $limit);
        }

        return $ids;
    }
}
