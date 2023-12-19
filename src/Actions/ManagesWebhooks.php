<?php

namespace TestMonitor\Jira\Actions;

use TestMonitor\Jira\Resources\Webhook;
use TestMonitor\Jira\Transforms\TransformsWebhooks;

trait ManagesWebhooks
{
    use TransformsWebhooks;

    /**
     * Get a list of webhooks.
     *
     * @param int $offset
     * @param int $limit
     *
     * @throws \TestMonitor\Jira\Exceptions\InvalidDataException
     *
     * @return \TestMonitor\Jira\Resources\Webhook[]
     */
    public function webhooks(int $offset = 0, int $limit = 50)
    {
        $response = $this->get('webhook', [
            'query' => [
                'startAt' => $offset,
                'maxResults' => $limit,
            ],
        ]);

        return $this->fromJiraWebhooks($response['values'] ?? []);
    }

    /**
     * Create a new webhook.
     *
     * @param Webhook $webhook
     * @return \TestMonitor\Jira\Resources\Webhook
     */
    public function createWebhook(Webhook $webhook): Webhook
    {
        $response = $this->post('webhook', ['json' => $this->toJiraWebhook($webhook)]);

        $result = array_shift($response['webhookRegistrationResult']);

        $webhook->id = $result['createdWebhookId'];

        return $webhook;
    }

    /**
     * Extend webhook lifetimes.
     *
     * @param array $webhookIds
     * @return string
     */
    public function extendWebhookLifetimes(array $webhookIds): bool
    {
        $response = $this->put('webhook/refresh', [
            'json' => ['webhookIds' => $webhookIds],
        ]);

        return empty($response);
    }

    /**
     * Delete webhooks.
     *
     * @param array $webhookIds
     * @return bool
     */
    public function deleteWebhooks(array $webhookIds): bool
    {
        $response = $this->delete('webhook', [
            'json' => ['webhookIds' => $webhookIds],
        ]);

        return empty($response);
    }
}
