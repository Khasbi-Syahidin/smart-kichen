<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Resources\Pages\Page;
use App\Models\Consumer;
use App\Models\AttendanceSession;
use App\Models\AttendanceConsumer;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;

class AttendanceCamera extends Page
{
    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.attendance-camera';

    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $title = 'Scan QR Absen';
    public ?string $scannedName = null;
    public $attendanceSessionId;
    public $attendanceSession;
    public $consumer;




    public function mount(): void
    {
        $this->attendanceSessionId = request()->query('record');
        $this->attendanceSession = AttendanceSession::findOrFail($this->attendanceSessionId);
        $this->scannedName = null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Akhiri Absensi')
                ->url(route('filament.admin.resources.attendances.index')),
        ];
    }

    public function markAttendance(): void
    {
        if (! $this->scannedName) {
            Notification::make()
                ->title('QR tidak valid')
                ->danger()
                ->send();
            return;
        }

        Log::info('name result scan: ' . $this->scannedName);
        $scannedName = trim(strtolower($this->scannedName));

        $consumer = Consumer::whereRaw('LOWER(TRIM(name)) = ?', [$scannedName])->first();

        Log::info('consumer query: ' . $consumer);
        if (!$consumer) {
            Notification::make()
                ->title('Consumer tidak ditemukan')
                ->danger()
                ->send();
            // $this->dispatch('rfidError', 'RFID tidak ditemukan'); // <-- dispatch di sini, benar
            // $this->reset('rfid');
            return;
        }

        $alreadyExists = AttendanceConsumer::where('attendance_session_id', $this->attendanceSessionId)
            ->where('consumer_id', $consumer->id)
            ->exists();

        if ($alreadyExists) {
            Notification::make()
                ->title("{$consumer->name} sudah absen sebelumnya")
                ->info()
                ->send();
            // $this->reset('rfid');
            return;
        } else {

            if ($consumer->is_active == false) {
                Notification::make()
                    ->title("{$consumer->name} berstatus tidak aktif")
                    ->danger()
                    ->send();
                // $this->reset('rfid');
                return;
            }
            if ($consumer->created_at > $this->attendanceSession->date) {
                Notification::make()
                    ->title("{$consumer->name} belum bergabung di sesi ini")
                    ->danger()
                    ->send();
                // $this->reset('rfid');
                return;
            }
            AttendanceConsumer::create([
                'attendance_session_id' => $this->attendanceSessionId,
                'consumer_id' => $consumer->id,
            ]);
        }

        $this->consumer = $consumer;
        Notification::make()
            ->title("{$consumer->name} berhasil absen")
            ->success()
            ->send();
        // $this->reset('rfid');
    }
}
