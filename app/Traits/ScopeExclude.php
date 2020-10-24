<?php
namespace App\Traits;

use App\Document;

trait ScopeExclude{
	public function scopeExclude($query, $value = array()){
		return $query->select(array_diff($this->columns, (array) $value));
	}
}