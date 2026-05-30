<?php

namespace App\Filament\Support;

use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Reusable CSV export helper for Filament table header actions.
 * No external package — streams the resource's main columns as CSV.
 */
class ExportsCsv
{
    /**
     * Build a header "Export CSV" action.
     *
     * @param  string  $filenamePrefix  e.g. "lien-ket"
     * @param  array<string,string>  $headers  map of CSV header label => row key (dot notation supported)
     * @param  callable():\Illuminate\Support\Collection|callable():iterable  $rowsResolver  returns the records to export
     */
    public static function action(string $filenamePrefix, array $headers, callable $rowsResolver): Action
    {
        return Action::make('exportCsv')
            ->label('Xuất CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function () use ($filenamePrefix, $headers, $rowsResolver): StreamedResponse {
                $filename = $filenamePrefix . '-' . now()->format('Y-m-d_His') . '.csv';

                return response()->streamDownload(function () use ($headers, $rowsResolver) {
                    $out = fopen('php://output', 'w');
                    // UTF-8 BOM so Excel reads Vietnamese correctly.
                    fwrite($out, "\xEF\xBB\xBF");
                    fputcsv($out, array_keys($headers));

                    foreach ($rowsResolver() as $record) {
                        $line = [];
                        foreach ($headers as $key) {
                            $line[] = data_get($record, $key);
                        }
                        fputcsv($out, $line);
                    }

                    fclose($out);
                }, $filename, [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            });
    }
}
