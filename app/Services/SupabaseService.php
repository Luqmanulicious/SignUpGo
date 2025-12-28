<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $headers;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
        $this->headers = [
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ];
    }

    public function from($table)
    {
        return new class($this->supabaseUrl, $table, $this->headers) {
            protected $url;
            protected $table;
            protected $headers;
            protected $queryParams = [];

            public function __construct($url, $table, $headers)
            {
                $this->url = $url;
                $this->table = $table;
                $this->headers = $headers;
            }

            public function select($columns = '*')
            {
                $this->queryParams['select'] = $columns;
                return $this;
            }

            public function eq($column, $value)
            {
                $this->queryParams[$column] = 'eq.' . $value;
                return $this;
            }

            public function get()
            {
                $response = Http::withHeaders($this->headers)
                    ->get("{$this->url}/rest/v1/{$this->table}", $this->queryParams);
                
                return $response->json();
            }

            public function insert($data)
            {
                $response = Http::withHeaders($this->headers)
                    ->post("{$this->url}/rest/v1/{$this->table}", $data);
                
                return $response->json();
            }

            public function update($data)
            {
                $response = Http::withHeaders($this->headers)
                    ->patch("{$this->url}/rest/v1/{$this->table}", $data);
                
                return $response->json();
            }

            public function delete()
            {
                $response = Http::withHeaders($this->headers)
                    ->delete("{$this->url}/rest/v1/{$this->table}");
                
                return $response->json();
            }
        };
    }
}