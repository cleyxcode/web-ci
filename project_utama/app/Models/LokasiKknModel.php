<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiKknModel extends Model
{
    protected $table            = 'lokasi_kkn';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_desa', 'kecamatan', 'kabupaten'];
    protected $useTimestamps = false;
}
