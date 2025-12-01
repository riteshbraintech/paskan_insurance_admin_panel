<?php

namespace App\Service\API;
use App\Models\Categoryformfield;
use App\Http\Resources\Api\V1\CategoryFieldResource;
use App\Models\CategoryFormFieldsOptionsRelation;

/**
 * Class HomeService
 *
 * Service for the API home/index endpoint.
 */
class HomeService
{
    /**
     * Return data for API index/home endpoint.
     *
     * @param array $params Optional parameters (filters, user context, etc)
     * @return array Structured payload to be returned by the controller
     */
    public static function getFirstQuestionOfCategory($request, $category)
    {
        try {

            $answerId = $request->answerId ?? null;
            $fieldId = $request->fieldId ?? null;

            // get first form fields
            $catId = $category->id ?? null;

            // get option ids list if answerId is provided
            $optionIds = [];
            if ($answerId) {
                CategoryFormFieldsOptionsRelation::where('parent_option_id', $answerId)
                    ->pluck('option_id')
                    ->each(function ($id) use (&$optionIds) {
                        $optionIds[] = $id;
                    });
            }


            // get first form field of category
            $formFieldsQuery = Categoryformfield::with(['translation','options','options.translation' ])->where('category_id', $catId)->orderBy('sort_order', 'asc');
                    

            // if optionIds is not empty then get form fields options where id in optionIds else get first form field of category
            if (!empty($optionIds) && is_array($optionIds)) {
                $formFieldsQuery->with(['options' => function($query) use ($optionIds) {
                    $query->whereIn('id', $optionIds);
                }]);
            }

            $formFields = $formFieldsQuery->paginate(1);

            // dd($formFields);
            $questionCount = Categoryformfield::where('category_id', $catId)->count();

            return [
                'totalQuestions' => $questionCount,
                'info' => CategoryFieldResource::collection($formFields)
            ];
            
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 1);
        }
    }

    /**
     * Return data for API index/home endpoint.
     *
     * @param array $params Optional parameters (filters, user context, etc)
     * @return array Structured payload to be returned by the controller
     */
    public static function getAlluestionOfCategory($request, $category)
    {
        try {
            // get first form fields
            $catId = $category->id ?? null;


            // get first form field of category
            $formFieldsQuery = Categoryformfield::with(['translation','options','options.translation' ])->where('category_id', $catId)->orderBy('sort_order', 'asc');

            $formFields = $formFieldsQuery->get();

            // dd($formFields);
            $questionCount = Categoryformfield::where('category_id', $catId)->count();

            return [
                'totalQuestions' => $questionCount,
                'info' => CategoryFieldResource::collection($formFields)
            ];
            
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), 1);
        }
    }

    /**
     * Alias of index() to satisfy "inde" naming if required.
     *
     * @param array $params
     * @return array
     */
    public function inde(array $params = []): array
    {
        return $this->index($params);
    }
}