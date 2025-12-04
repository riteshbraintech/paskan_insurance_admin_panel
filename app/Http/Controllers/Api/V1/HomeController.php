<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BannerResource;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Banner;
use App\Models\Categoryformfield;
use App\Models\CMSPage;
use App\Http\Resources\Api\V1\CategoryFieldResource;
use App\Http\Resources\Api\V1\HelpmenuResource;
use App\Http\Resources\Api\V1\InsuranceClaimFAQResource;
use App\Models\Contact;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Notification;
use App\Models\User;
use App\Service\API\HomeService;
use App\Http\Requests\Api\V1\CategoryInqueirySubmit;
use App\Models\User;
use App\Models\UserEnquery;
use App\Models\UserInsuranceFillup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Api\V1\CategoryResourceWithDesc;

class HomeController extends Controller
{
    // create home function and reposen as json for api
    public function home(Request $request)
    {
        $lang = $request->lang;

        // You may tweak queries: eager load, caching, per-page limits, etc.
        // 1️⃣ Get top 4 categories
        $topcategories = Category::with('translation')->where('is_link', 1)
            ->active()
            ->orderBy('created_at', 'asc') // or by any other priority column
            ->limit(4)
            ->get();

        // 2️⃣ Get 4 random categories excluding top ones
        $randomCategories = Category::with('translation')
            ->active()
            ->whereNotIn('id', $topcategories->pluck('id')) // exclude top 4
            ->inRandomOrder()
            ->limit(4)
            ->get();

            
        $banners = Banner::with('translation')->where('is_active',1)->orderby('sort_order','asc')->get();
         // Fetch banners as needed
        $testimonials = []; // Fetch testimonials as needed
        $insurances = []; // Fetch insurances as needed
        $helpmenu= Insurance::with('translation')->where('is_published',1)->get(); //Fetch insurances for faqs
        // dd($helpmenu);
        
        $payload = [
            'banners' => BannerResource::collection($banners),
            'topcategories' => CategoryResource::collection($topcategories),
            'categories' => CategoryResource::collection($randomCategories),
            'testimonials' => $testimonials,
            'insurances' => $insurances,
            'helpmenu' => HelpmenuResource::collection($helpmenu),
        ];


        return response()->json(
            [
                'status' => 'success',
                'message' => 'Home data fetched successfully.',
                'data' => $payload,
            ],
            200
        );

    }


    // create home function and reposen as json for api
    public function headerMenu(Request $request)
    {
        $lang = $request->lang;

        // 3️⃣ Get all active categories if needed
        $allCategories = Category::with('translation')
            ->active()
            ->get();

        $payload = [
            'allCategories' => CategoryResource::collection($allCategories),
        ];

        return response()->json(
            [
                'status' => 'success',
                'message' => 'headerMenu data fetched successfully.',
                'data' => $payload,
            ],
            200
        );

    }


    // global single file upload function
    public function uploadSingleFiles(Request $request)
    {
        $filename = null;

        // Handle image upload
        if ($request->hasFile('document')) {
            $filename = time() . '.' . $request->document->extension();
            $request->document->move(public_path('tempfiles'), $filename);
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'File upload endpoint.',
                'data' => [
                    'file_name' => $filename,
                    'file_path' => url('tempfiles/' . $filename),
                ]
            ]
        );
    }

    // global multiple file upload function
    public function uploadMultipleFiles(Request $request)
    {
        $fileNames = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('tempfiles'), $filename);
                $fileNames[] = [
                    'file_name' => $filename,
                    'file_path' => url('tempfiles/' . $filename),
                ];
            }
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Multiple file upload endpoint.',
                'data' => $fileNames,
            ]
        );
    }

    // get CMS page by slug
    public function getCMSPage($slug)
    {
        $cmsPage = CMSPage::with('translation')->where('page_slug', $slug)->where('is_published', 1)->first();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'CMS page fetched successfully.',
                'data' => $cmsPage ?? null,
            ],
            200
        );

    }


    //get Insurance Claim FAQs By Insurance slug
    public function getinsuranceclaimfaqs($slug){
        $insurance = Insurance::where('slug', $slug)->where('is_published', 1)->first();
        $insuranceclaimfaqs = InsuranceClaim::with('translation')->where('insurance_id', $insurance->id)->where('is_published',1)->orderBy('sort_order', 'asc')->get();
        
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Insurance Claim FAQs fetched successfully.',
                'data' => InsuranceClaimFAQResource::collection($insuranceclaimfaqs),
            ],
            200
        );
    }


    // get category form fields by slug with pagination.
    public function categoryDynamicFormFields(Request $request, $slug)
    {
        try {

            // get category by slug
            $category = Category::where('slug', $slug)->where('is_active', 1)->firstOrFail();

            // get first form fields
            $question = HomeService::getFirstQuestionOfCategory($request, $category);

            return response()->json([
                'status'  => true,
                'message' => 'Form Fields fetched successfully.',
                'data'    => [
                    'category'  => new CategoryResource($category),
                    'question'  => $question,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // get category form fields by slug with pagination.
    public function categoryAllDynamicFormFields(Request $request, $slug)
    {
        try {

            // get category by slug
            $category = Category::where('slug', $slug)->where('is_active', 1)->firstOrFail();

            // get first form fields
            $question = HomeService::getAlluestionOfCategory($request, $category);

            return response()->json([
                'status'  => true,
                'message' => 'Form Fields fetched successfully.',
                'data'    => [
                    'category'  => new CategoryResourceWithDesc($category),
                    'question'  => $question,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    //Fetch The Contact Form and Saved
    public function contactform(Request $request)
    {
        $request->validate([
            'fullname'    => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phonenumber' => 'required|string|max:20',
            'subject'     => 'required|string|max:255',
            'message'     => 'required|string',
        ]);

        $userId = auth('sanctum')->id(); 
        
        $contact = Contact::create([
            'user_id'     => $userId,       
            'fullname'    => $request->fullname,
            'email'       => $request->email,
            'phonenumber' => $request->phonenumber,
            'subject'     => $request->subject,
            'message'     => $request->message,
        ]);

        
        // --------------- NOTIFICATION LOGIC -----------------
        
        // Find all admins
        $admins = User::where('id', 1)->get(); // replace with actual admin user(s)

        foreach($admins as $admin){
            // Save notification in your table
            $notification = Notification::create([
                'user_id' => $admin->id,
                'type'    => 'new_enquiry',
                'title'   => 'New Insurance Enquiry',
                'message' => "New enquiry from {$request->fullname} ({$request->email})",
                // 'link'    => route('admin.contact.show', $contact->id), // Adjust route
            ]);

            // Broadcast notification via Pusher
            event(new NewNotificationEvent($notification, "New message received!"));

        }

        // ---------------------------------------------------


        return response()->json([
            'status'  => true,
            'message' => 'Your inquiry has been submitted successfully.',
            'data'    => $contact,
        ], 200);
    }


    // categorySubmitEnquiry
    public function categorySubmitEnquiry(CategoryInqueirySubmit $request)
    {
        try {
            
            // check if validation passed and user_id is null then create user and save enquiry data
            $userId = $request->input('user_id');
            if (is_null($userId)) {
                $emaildata = $request->input('user.email');
                // check if user already exists with email
                $existingUser = User::where('email', $emaildata)->first();
                if ($existingUser) {
                    $userId = $existingUser->id;
                }else{
                    $user = User::create([
                        'name' => $request->input('user.name'),
                        'email' => $request->input('user.email'),
                        'phone' => $request->input('user.phone'),
                        'password' => Hash::make('defaultpassword'), // set a default password or generate one
                    ]);
                    $userId = $user->id;
                }
            }

            // Now save the enquiry data
            $categoryId = $request->input('category_id');

            $userEnquery = UserEnquery::create([
                'user_id' => $userId,
                'category_id' => $categoryId,
                'enqury_time' => now(),
                'status' => 'new',
            ]);

            $choosenAnswers = $request->input('choosenAnswer');
            foreach ($choosenAnswers as $fieldId => $answer) {
                UserInsuranceFillup::create([
                    'user_insurance_enqueries_id' => $userEnquery->id,
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                    'form_field_id' => $answer['form_field_id'] ?? '',
                    'form_field_name' => $answer['form_field_name'] ?? '', // You can fetch and set the form field name if needed
                    'form_field_value' => $answer['form_field_value'] ?? '',
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Your inquiry has been submitted successfully. Our Agent will contact you soon.',
            ], 200);

        //code...
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ], 500);
        }
        
    }

    // get insurance list based on user fillup and category
    public function categoryInsuranceQuotationList(Request $request, $slug)
    {
        try {
           
            // get category by slug
            $category = Category::where('slug', $slug)->where('is_active', 1)->firstOrFail();

            // get insurances based on category and user fillup data
            $insurances = HomeService::getInsuranceQuotationList($category, $request);

            return response()->json([
                'status'  => true,
                'message' => 'Insurance Quotation List fetched successfully.',
                'data'    => $insurances,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ], 500);
        }

    }

}
