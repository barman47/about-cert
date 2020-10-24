<?php
namespace App\Traits;

trait CustomPaginate{
    private $customPaginateWhereIn = array();

    public function customPaginate($count = 20, $page = 1){
        $totalBuilder = $this->customPaginateDataCount();

        foreach($this->customPaginateWhereIn as $key => $value){
            $totalBuilder->whereIn($key, $value);
        }

        $total = $totalBuilder->count();

        $perPage = $count;
        $currentPage = $page;
        $pageCount = ceil($total / $count);
        $firstPageUrl = url()->current() . "?count=$count&page=1";
        $lastPageUrl =  url()->current() . "?count=$count&page=$pageCount";

        $prevPageUrl = $page > 1 ? url()->current() . "?count=$count&page=" . ($page - 1) : null;
        $path = url()->current();
        $from = 1;
        $to = $pageCount;

        $data = $this->customPaginateDataOrder($this->customPaginateDataBuilder());

        foreach($this->customPaginateWhereIn as $key => $value){
            $data->whereIn($key, $value);
        }

        $data = $data
                ->skip($count * ($page - 1))
                ->take($count)->get() ?? [];

        // $returnCount = $data->count();

        return (object)[
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "first_page_url" => $firstPageUrl,
            "last_page_url" => $lastPageUrl,
            "prev_page_url" => $prevPageUrl,
            "path" => $path,
            "from" => $from,
            "to" => $to,
            "data" => $data
        ];
    }//end method customPaginate

    public function customPaginateWhereIn(string $key, array $array){
        $this->customPaginateWhereIn[$key] = $array;
        return $this;
    }//end method customPaginateWhereIn

    private function customPaginateDataBuilder(){
        return static::query();
    }//end method customPaginateData

    private function customPaginateDataOrder($builder){
        return $builder->latest();
    }//end method customPaginateDataOrder

    private function customPaginateDataCount(){
        return $this->customPaginateDataBuilder();
    }//end method customPaginateDataCount
}//end trait CustomPaginate
