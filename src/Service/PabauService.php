<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PabauService
{
	private HttpClientInterface $client;
	private CacheInterface $cache;
	private string $token;

	public function __construct(HttpClientInterface $client, CacheInterface $cacheInterface, string $pabauToken)
	{
		$this->client = $client;
		$this->token = $pabauToken;
		$this->cache = $cacheInterface;
	}

	/**
	 * Получить все treatments из Pabau
	 */
	public function getAllCategories(): array
	{
		return $this->cache->get('pabau_categories', function (ItemInterface $item) {
			$item->expiresAfter(7200); // кеш 2 часа

			$url = "https://api.oauth.pabau.com/{$this->token}/categories/services";

			$response = $this->client->request('GET', $url, [
				'headers' => ['Accept' => 'application/json']
			]);

			$data = $response->toArray();

			return $data['service_categories'] ?? [];
		});
	}
	private function normalize(string $name): string
	{
		$name = strtolower($name);                    // в lowercase
		$name = trim($name);                          // убрать пробелы
		$name = preg_replace('/\s+/', ' ', $name);    // убрать лишние пробелы
		$name = preg_replace('/[^a-z0-9\s]/', '', $name); // убрать спецсимволы: (), -, /
		return $name;
	}

	/**
	 * Найти category_id по НАЗВАНИЮ услуги
	 */
	public function findCategoryIdByName(string $name): ?int
	{
		$normalizedTarget = $this->normalize($name);

		foreach ($this->getAllCategories() as $t) {
			$normalizedApiName = $this->normalize($t['name']);

			if ($normalizedApiName === $normalizedTarget) {
				return $t['id'];
			}
		}

		return null;
	}

}
