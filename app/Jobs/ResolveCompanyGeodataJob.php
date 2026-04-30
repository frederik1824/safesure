<?php

namespace App\Jobs;

use App\Models\Empresa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResolveCompanyGeodataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $empresaId;
    protected $url;

    public $tries = 3;
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(int $empresaId, string $url)
    {
        $this->empresaId = $empresaId;
        $this->url = $url;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $empresa = Empresa::find($this->empresaId);
        if (!$empresa) return;

        try {
            // Resolve redirect if it's a short URL
            $response = Http::withOptions(['allow_redirects' => true])->get($this->url);
            $finalUrl = $response->effectiveUri()->__toString();

            // Extract coordinates from URL string (@lat,lng)
            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $empresa->update([
                    'latitude' => $matches[1],
                    'longitude' => $matches[2]
                ]);
                Log::info("Geodata resolved for Company ID {$this->empresaId}");
                return;
            }
            
            // Extract from query params (ll=lat,lng)
            if (preg_match('/ll=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $empresa->update([
                    'latitude' => $matches[1],
                    'longitude' => $matches[2]
                ]);
                return;
            }

        } catch (\Exception $e) {
            Log::warning("Could not resolve geodata for Company ID {$this->empresaId}. Error: " . $e->getMessage());
            throw $e;
        }
    }
}
