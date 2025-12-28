<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Category;
use App\Models\Design;
use App\Models\Fabric;
use App\Models\SizeGown;
use App\Models\User;
use App\Models\SizeTypeCategory;
use Auth;
use Illuminate\Http\Request;
use App\Models\SizeTypeCategoryUser;
use Illuminate\Support\Facades\Log;

class ListController extends Controller
{
    use Helper;

    public function __construct()
    {
        if (app('request')->header('lang')) {
            app()->setLocale(app('request')->header('lang'));
        }
    }

    // ⬇️ KEEP ALL YOUR EXISTING METHODS - listCateogry, listDesign, listFabric, sizeGown
    public function listCateogry()
    {
        try {
            $categories = Category::paginate(10);
            if ($categories->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $categories);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listDesign()
    {
        try {
            $designs = Design::with('images')->get();
            if ($designs->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $designs);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listFabric()
    {
        try {
            $fabrics = Fabric::get();
            if ($fabrics->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $fabrics);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function sizeGown(Request $request)
    {
        try {
            $sizeGowns = SizeGown::with('images', 'sizeGownOptions');
            if($request->type){
                $sizeGowns = $sizeGowns->where('type',$request->type);
            }
            $sizeGowns = $sizeGowns->get();
            if ($sizeGowns->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeGowns);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }
    // ⬆️ END OF UNCHANGED METHODS

    // ⬇️ REPLACE ONLY THIS METHOD
    public function sizeTypeCategoryUser(Request $request)
    {
        try {
            // Get user_id from request or use authenticated user
            $userId = $request->user_id ?? Auth::id();
            
            Log::info('🔍 [API] Fetching size types for user', [
                'user_id' => $userId,
                'request_has_user_id' => $request->has('user_id')
            ]);
            
            // Validate user exists
            $user = User::find($userId);
            if (!$user) {
                Log::warning('⚠️ [API] User not found', ['user_id' => $userId]);
                return $this->outApiJson('user-not-found', trans('main.user_not_found'));
            }
            
            Log::info('✅ [API] User found', [
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);
            
            // Get all categories with their size types
            $categories = Category::with(['sizeTypes'])->get();
            
            Log::info('📊 [API] Categories fetched', [
                'total_categories' => $categories->count()
            ]);
            
            // Transform the data to include user values
            $result = $categories->map(function($category) use ($userId) {
                
                Log::info('🔄 [API] Processing category', [
                    'category_id' => $category->id,
                    'category_name' => $category->name ?? 'Unknown'
                ]);
                
                // Get size types for this category
                $sizeTypesData = $category->sizeTypes->map(function($sizeType) use ($userId, $category) {
                    
                    // Get the size_type_category_id from the pivot table
                    $sizeTypeCategoryId = $sizeType->pivot->id;
                    
                    Log::info('   📏 [API] Processing size type', [
                        'size_type_id' => $sizeType->id,
                        'size_type_name' => $sizeType->name ?? 'Unknown',
                        'size_type_category_id' => $sizeTypeCategoryId,
                        'category_id' => $category->id
                    ]);
                    
                    // Get user value for this specific size_type_category
                    $userValue = SizeTypeCategoryUser::where('size_type_category_id', $sizeTypeCategoryId)
                        ->where('user_id', $userId)
                        ->first();
                    
                    if ($userValue) {
                        Log::info('      ✓ [API] User value found', [
                            'size_type_category_id' => $sizeTypeCategoryId,
                            'value' => $userValue->value
                        ]);
                    } else {
                        Log::info('      ✗ [API] No user value found', [
                            'size_type_category_id' => $sizeTypeCategoryId
                        ]);
                    }
                    
                    return [
                        'size_type_id' => $sizeType->id,
                        'size_type_name' => $sizeType->name ?? 'Unknown',
                        'size_type_category_id' => $sizeTypeCategoryId,
                        'user_value' => $userValue ? $userValue->value : null,
                        'has_value' => $userValue ? true : false,
                    ];
                });
                
                Log::info('✅ [API] Category processed', [
                    'category_id' => $category->id,
                    'total_size_types' => $sizeTypesData->count(),
                    'size_types_with_values' => $sizeTypesData->where('has_value', true)->count()
                ]);
                
                return [
                    'id' => $category->id,
                    'name' => $category->name ?? 'Unknown',
                    'size_types' => $sizeTypesData->values()->toArray()
                ];
            });
            
            // Calculate statistics
            $totalSizeTypes = $result->sum(function($cat) {
                return count($cat['size_types']);
            });
            
            $totalWithValues = $result->sum(function($cat) {
                return collect($cat['size_types'])->where('has_value', true)->count();
            });
            
            Log::info('🎉 [API] Size types fetched successfully', [
                'user_id' => $userId,
                'total_categories' => $result->count(),
                'total_size_types' => $totalSizeTypes,
                'size_types_with_values' => $totalWithValues,
                'size_types_without_values' => $totalSizeTypes - $totalWithValues
            ]);
            
            if ($result->isEmpty()) {
                Log::warning('⚠️ [API] No data found', ['user_id' => $userId]);
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            
            return $this->outApiJson('success', trans('main.success'), $result);
            
        } catch (\Exception $th) {
            Log::error('❌ [API] Exception in sizeTypeCategoryUser', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }
    // ⬆️ END OF REPLACEMENT
}