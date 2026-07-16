<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalReferenceText extends Model
{
    protected $table = 'proposal_reference_texts';

    protected $fillable = [
        'section_key',
        'contoh_teks',
        'embedding_vector',
    ];

    protected $casts = [
        'embedding_vector' => 'array',
    ];
}
