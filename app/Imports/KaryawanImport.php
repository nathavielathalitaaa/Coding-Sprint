<?php

namespace App\Imports;

use DB;
use Hash;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KaryawanImport
{
    public int   $successCount = 0;
    public int   $failedCount  = 0;
    public array $failedRows   = [];

    /** Expected heading columns (row 1 of the Excel file). */
    private array $columns = [
        'nama', 'email', 'role', 'password',
        'departemen', 'posisi', 'jabatan_approval', 'status_akun',
        'pendidikan_terakhir', 'no_telepon', 'tgl_bergabung', 'tgl_akhir_kontrak',
        'nik', 'no_kk', 'npwp', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan',
        'status_pernikahan', 'jumlah_anak', 'jenis_kelamin',
        'tempat_lahir', 'tgl_lahir', 'alamat', 'kota', 'provinsi',
    ];

    // ── Entry point ────────────────────────────────────────────────────────
    public function import(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getSheet(0);
        $rows        = $sheet->toArray(null, true, true, true); // A-indexed

        $headers = null;
        foreach ($rows as $rowNum => $rawRow) {
            $row = array_values($rawRow);

            if ($rowNum === 1) {
                $headers = array_map(fn($h) => strtolower(trim((string)$h)), $row);
                continue;
            }

            // Skip example row if it exists (usually row 2)
            if ($rowNum === 2 && str_contains(strtolower((string)$row[0] ?? ''), 'budi santoso')) {
                continue;
            }

            $nonEmpty = array_filter($row, fn($v) => $v !== null && trim((string)$v) !== '');
            if (empty($nonEmpty)) {
                continue;
            }

            $data = [];
            if ($headers) {
                foreach ($headers as $idx => $col) {
                    if (empty($col)) continue;
                    $data[$col] = isset($row[$idx]) ? trim((string)$row[$idx]) : null;
                }
            } else {
                foreach ($this->columns as $idx => $col) {
                    $data[$col] = isset($row[$idx]) ? trim((string)$row[$idx]) : null;
                }
            }

            $this->processRow($rowNum, $data);
        }
    }

    // ── Process one row ────────────────────────────────────────────────────
    private function processRow(int $rowNum, array $data): void
    {
        $validator = Validator::make($data, [
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role'  => 'required|in:Anggota,BPH,Pembina',
        ], [
            'nama.required'  => 'Kolom nama wajib diisi.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
            'role.required'  => 'Kolom role wajib diisi.',
            'role.in'        => 'Role harus BPH, Anggota, atau Pembina.',
        ]);

        if ($validator->fails()) {
            $this->failedCount++;
            $this->failedRows[] = [
                'row'    => $rowNum,
                'nama'   => $data['nama'] ?? '(kosong)',
                'email'  => $data['email'] ?? '(kosong)',
                'reason' => implode(' | ', $validator->errors()->all()),
            ];
            return;
        }

        try {
            DB::beginTransaction();

            $roleName = trim($data['role']);
            $password = !empty($data['password']) ? $data['password'] : 'Sinergi@2026';

            // 1. Save to users table
            $user = User::create([
                'name'     => $data['nama'],
                'email'    => $data['email'],
                'password' => Hash::make($password),
                'role_name'=> $roleName,
                'status'   => $data['status_akun'] ?? 'Active',
                'avatar'   => 'profile.png',
                'must_change_password' => true,
            ]);

            // Auto generate employee_id (stored in user_id column)
            $count = User::where('user_id', 'LIKE', 'SIN-%')->count() + 1;
            $user->user_id = 'SIN-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            $user->saveQuietly();

            $user->assignRole($roleName);

            // 2. Save to employee_profiles table
            EmployeeProfile::create([
                'user_id'              => $user->id,
                'departemen'           => $data['departemen'] ?? null,
                'posisi'               => $data['posisi'] ?? null,
                // jabatan_approval disimpan sebagai label jabatan di PDF.
                // Assignment sebagai approver dilakukan secara manual
                // oleh HR melalui menu Kelola Jenis Surat.
                'jabatan'              => $data['jabatan_approval'] ?? null,
                'status'               => $data['status_akun'] ?? 'Active',
                'pendidikan_terakhir'  => $data['pendidikan_terakhir'] ?? null,
                'no_telepon'           => $data['no_telepon'] ?? null,
                'tgl_bergabung'        => $this->parseDate($data['tgl_bergabung'] ?? null),
                'tgl_kontrak_akhir'    => $this->parseDate($data['tgl_akhir_kontrak'] ?? null),
                'nik'                  => $data['nik'] ?? null,
                'no_kk'                => $data['no_kk'] ?? null,
                'npwp'                 => $data['npwp'] ?? null,
                'bpjs_kesehatan'       => $data['bpjs_kesehatan'] ?? null,
                'bpjs_ketenagakerjaan' => $data['bpjs_ketenagakerjaan'] ?? null,
                'status_pernikahan'    => $this->mapStatusPernikahan($data['status_pernikahan'] ?? null),
                'jumlah_anak'          => is_numeric($data['jumlah_anak'] ?? null) ? (int)$data['jumlah_anak'] : 0,
                'jenis_kelamin'        => $data['jenis_kelamin'] ?? null,
                'tempat_lahir'         => $data['tempat_lahir'] ?? null,
                'tgl_lahir'            => $this->parseDate($data['tgl_lahir'] ?? null),
                'alamat'               => $data['alamat'] ?? null,
                'kota'                 => $data['kota'] ?? null,
                'provinsi'             => $data['provinsi'] ?? null,
            ]);

            DB::commit();
            $this->successCount++;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->failedCount++;
            $this->failedRows[] = [
                'row'    => $rowNum,
                'nama'   => $data['nama'] ?? '(kosong)',
                'email'  => $data['email'] ?? '(kosong)',
                'reason' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function getResult(): array
    {
        return [
            'success'     => $this->successCount,
            'failed'      => $this->failedCount,
            'failed_rows' => $this->failedRows,
        ];
    }

    private function parseDate(?string $value): ?string
    {
        if (empty($value)) return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function mapStatusPernikahan(?string $value): string
    {
        return match(strtolower(trim($value ?? ''))) {
            'married', 'menikah'         => 'menikah',
            'divorced', 'cerai'          => 'cerai_hidup',
            'widowed', 'cerai_mati'      => 'cerai_mati',
            default                      => 'belum_menikah',
        };
    }
}
