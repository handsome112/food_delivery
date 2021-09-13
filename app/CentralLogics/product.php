<?php

namespace App\CentralLogics;


use App\Models\Food;
use App\Models\Review;

class ProductLogic
{
    public static function get_product($id)
    {
        return Food::active()->with(['rating'])->where('id', $id)->first();
    }

    public static function get_latest_products($limit = null, $offset = null, $restaurant_id)
    {
        if($limit && $offset)
        {
            $paginator = Food::active()->with(['rating'])->where('restaurant_id', $restaurant_id)->latest()->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
            return [
                'total_size' => $paginator->total(),
                'limit' => $limit,
                'offset' => $offset,
                'products' => $paginator->items()
            ];
        }
        $products = Food::active()->with(['rating'])->where('restaurant_id', $restaurant_id)->latest()->get();        
        return [
            'total_size' => null,
            'limit' => $limit,
            'offset' => $offset,
            'products' => $products
        ];
    }

    public static function get_related_products($product_id)
    {
        $product = Food::find($product_id);
        return Food::active()->with(['rating'])->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products($name, $zone_id, $limit = 10, $offset = 1)
    {
        $key = explode(' ', $name);
        $paginator = Food::active()->with(['rating'])->whereHas('restaurant', function($q)use($zone_id){
            $q->where('zone_id', $zone_id);
        })->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }
    
    public static function popular_products($zone_id, $limit = null, $offset = null)
    {
        if($limit != null && $offset != null)
        {
            $paginator = Food::with(['rating'])->whereHas('restaurant', function($q)use($zone_id){
                $q->where('zone_id', $zone_id);
            })->active()->popular()->paginate($limit, ['*'], 'page', $offset);

            return [
                'total_size' => $paginator->total(),
                'limit' => $limit,
                'offset' => $offset,
                'products' => $paginator->items()
            ];
        }
        $paginator = Food::active()->with(['rating'])->whereHas('restaurant', function($q)use($zone_id){
            $q->where('zone_id', $zone_id);
        })->withCount('orders')->orderBy('orders_count', 'desc')->limit(50)->get();

        return [
            'total_size' => $paginator->count(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator
        ];
        
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }
}