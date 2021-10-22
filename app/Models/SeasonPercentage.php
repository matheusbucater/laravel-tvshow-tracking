<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonPercentage extends Model
{
    use HasFactory;

    protected $table = 'seasons_percentages';
    protected $attributes = [
        'percentage' => 0
    ];
    protected $fillable = [
        'tvshow_id',
        'season_number'
    ];

    public function storeSeasons() {
        $tvshow = TvShow::find($this->tvshow_id);
//        foreach ($tvshow->getSeasonsNumbers() as $season_number) {
//            if (!$this->get()->contains($season_number)) {
//                $percentage = $this->newInstance();
//                $percentage->tvshow_id = $this->tvshow_id;
//                $percentage->season_number = $season_number;
//                $percentage->save();
//            }
//        }
        for ($s = $tvshow->getSeasonsNumbers()[0]; $s <= $tvshow->getSeasonsNumbers()[1]; $s++) {
            if (!$this->get()->contains($s)) {
                $percentage = $this->newInstance();
                $percentage->tvshow_id = $this->tvshow_id;
                $percentage->season_number = $s;
                $percentage->save();
            }
        }
    }

    public function storePercentage() {
        $tvshow = TvShow::find($this->tvshow_id);
        $this->percentage = $tvshow->getSeasonPercentage($this->season_number);
        $this->save();
    }

    public function getPercentage() {
        return $this->percentage;
    }
}
