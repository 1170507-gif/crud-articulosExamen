<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReporteArticulos
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    
    }

    public function handle(): void
    {
        Log::info('=========================================');
        Log::info('JOB EJECUTADO EXITOSAMENTE');
        Log::info('Fecha y hora: ' . now()->format('Y-m-d H:i:s'));
        Log::info('Este es un reporte automático de artículos');
        Log::info('=========================================');
   
        echo "Job ejecutado correctamente!\n";
        echo "Revisa: storage/logs/laravel.log\n";
    }
}