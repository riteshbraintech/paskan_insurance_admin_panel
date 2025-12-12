<?php

namespace App\Service\API;
use App\Models\Categoryformfield;
use App\Http\Resources\Api\V1\CategoryFieldResource;
use App\Models\CategoryFormFieldsOptionsRelation;
use App\Service\ViriyahAuthService;
use App\Http\Resources\Api\V1\CarInsuranceQuotationResource;



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



    public static function getInsuranceQuotationList($category, $request)
    {


        $payload = [
            // "agentCode"       => "09865",
            // "energyType"      => "C",
            "carBrand"        => $request->carBrand,
            "carModel"        => $request->carModel,
            // "carSubModel"     => $request->carSubModel,
            // "registrationYear"=> $request->registrationYear,
            // "vehicleTypeCode" => [
            //     "110",
            //     "120",
            //     "210",
            //     "220",
            //     "320"
            // ],
        ];
        // dd($payload);

        $viriyahClass = new ViriyahAuthService();
        // $quotation = $viriyahClass->getMotorQuotation($payload);
        // dd($quotation);

        // return response()->json([$quotation, $payload]);
        
        // Implement your logic here to fetch and structure the data
        $sampleResponse = json_decode(Self::sampleResponse(), true);

        $data = [
            // 'quotation' => CarInsuranceQuotationResource::collection($sampleResponse['data']),
            'quotation' => CarInsuranceQuotationResource::collection(collect($sampleResponse['data'])->toArray()),
            'timestamp' => now(),
            // Add more data as needed
        ];

        return $data;
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


    // dummy sample response
    public static function sampleResponse()
    {
        return '
        {"integrationStatusCode":"0","data":[{"quotationNumber":"Q15841BX8G20240604T17303043300","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX8","packageName":"แคมเปญพิเศษงานประกันภัยใหม่และต่ออายุมีเคลม ประเภท 3 Single Rate (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"3","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"0","deductible":"0","tfSumInsured":"0"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"4","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":2326.45,"stamp":10,"vat":163.55,"totalPremium":2500},{"quotationNumber":"Q15841BXIG20240604T17303043301","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BXI","packageName":"แคมเปญประกันใหม่ และต่ออายุมีเคลม ป.3 รถชนรถฝ่ายถูก (มีส่วนลดCCTVและAPPแล้ว)","insuranceType":"3","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"0","deductible":"0","tfSumInsured":"0"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"4","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":3443.94,"stamp":14,"vat":242.06,"totalPremium":3700},{"quotationNumber":"Q15841BXCG20240604T17303043302","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BXC","packageName":"แคมเปญพิเศษประเภท 2 Single Rate (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"2","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"100000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"4","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":3257.03,"stamp":14,"vat":228.97,"totalPremium":3500},{"quotationNumber":"Q15841BX2G20240604T17303043303","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX2","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"0"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6143.22,"stamp":25,"vat":431.78,"totalPremium":6600},{"quotationNumber":"Q15841EXBG20240604T17303043304","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXB","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"100000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6143.22,"stamp":25,"vat":431.78,"totalPremium":6600},{"quotationNumber":"Q15841BW9G20240604T17303043305","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BW9","packageName":"ประเภท 5 (3+) SINGLE RATE โครงการพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"100000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"300000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6608.51,"stamp":27,"vat":464.49,"totalPremium":7100},{"quotationNumber":"Q15841BX1G20240604T17303043306","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX1","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"100000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6981.35,"stamp":28,"vat":490.65,"totalPremium":7500},{"quotationNumber":"Q15841EXAG20240604T17303043307","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXA","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"100000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6981.35,"stamp":28,"vat":490.65,"totalPremium":7500},{"quotationNumber":"Q15841BW8G20240604T17303043308","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BW8","packageName":"ประเภท 5 (2+) SINGLE RATE โครงการพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"100000","deductible":"0","tfSumInsured":"100000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"300000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7446.64,"stamp":30,"vat":523.36,"totalPremium":8000},{"quotationNumber":"Q15841BX2G20240604T17303043309","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX2","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"150000","deductible":"0","tfSumInsured":"0"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6794.43,"stamp":28,"vat":477.57,"totalPremium":7300},{"quotationNumber":"Q15841EXBG20240604T17303043310","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXB","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"150000","deductible":"0","tfSumInsured":"150000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":6794.43,"stamp":28,"vat":477.57,"totalPremium":7300},{"quotationNumber":"Q15841BX1G20240604T17303043311","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX1","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"150000","deductible":"0","tfSumInsured":"150000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7632.55,"stamp":31,"vat":536.45,"totalPremium":8200},{"quotationNumber":"Q15841EXAG20240604T17303043312","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXA","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"150000","deductible":"0","tfSumInsured":"150000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7632.55,"stamp":31,"vat":536.45,"totalPremium":8200},{"quotationNumber":"Q15841BX2G20240604T17303043313","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX2","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"200000","deductible":"0","tfSumInsured":"0"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7167.26,"stamp":29,"vat":503.74,"totalPremium":7700},{"quotationNumber":"Q15841EXBG20240604T17303043314","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXB","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"200000","deductible":"0","tfSumInsured":"200000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7167.26,"stamp":29,"vat":503.74,"totalPremium":7700},{"quotationNumber":"Q15841BW9G20240604T17303043315","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BW9","packageName":"ประเภท 5 (3+) SINGLE RATE โครงการพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"200000","deductible":"0","tfSumInsured":"200000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"300000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7632.55,"stamp":31,"vat":536.45,"totalPremium":8200},{"quotationNumber":"Q15841BX1G20240604T17303043316","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX1","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"200000","deductible":"0","tfSumInsured":"200000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":8191.3,"stamp":33,"vat":575.7,"totalPremium":8800},{"quotationNumber":"Q15841EXAG20240604T17303043317","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXA","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"200000","deductible":"0","tfSumInsured":"200000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":8191.3,"stamp":33,"vat":575.7,"totalPremium":8800},{"quotationNumber":"Q15841BW8G20240604T17303043318","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BW8","packageName":"ประเภท 5 (2+) SINGLE RATE โครงการพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"200000","deductible":"0","tfSumInsured":"200000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"300000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":8656.59,"stamp":35,"vat":608.41,"totalPremium":9300},{"quotationNumber":"Q15841BX2G20240604T17303043319","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX2","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"250000","deductible":"0","tfSumInsured":"0"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7726.01,"stamp":31,"vat":542.99,"totalPremium":8300},{"quotationNumber":"Q15841EXBG20240604T17303043320","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXB","packageName":"โครงการประเภท 5 (3+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"3P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"250000","deductible":"0","tfSumInsured":"250000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":7726.01,"stamp":31,"vat":542.99,"totalPremium":8300},{"quotationNumber":"Q15841BX1G20240604T17303043321","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"BX1","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษ (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"250000","deductible":"0","tfSumInsured":"250000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":8750.05,"stamp":35,"vat":614.95,"totalPremium":9400},{"quotationNumber":"Q15841EXAG20240604T17303043322","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EXA","packageName":"โครงการประเภท 5 (2+) SINGLE RATE แคมเปญพิเศษระยะสั้น (มีส่วนลด CCTV และAPP แล้ว)","insuranceType":"2P","repairType":"G","liability":{"tpbiPerPerson":"500000","tpbiPerEvent":"10000000","tppdPerEvent":"1000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"250000","deductible":"0","tfSumInsured":"250000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"50000","passengerDeath":"50000","numberPassengerDeath":"5","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"50000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":8750.05,"stamp":35,"vat":614.95,"totalPremium":9400},{"quotationNumber":"Q15841X36G20240604T17303043323","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"X36","packageName":"งานประกันใหม่/ต่ออายุต่างบริษัท(เดิมX21) STD (6-10ปี) (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"1","repairType":"G","liability":{"tpbiPerPerson":"1000000","tpbiPerEvent":"10000000","tppdPerEvent":"5000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"280000","deductible":"0","tfSumInsured":"280000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"200000","passengerDeath":"200000","numberPassengerDeath":"6","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"200000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"อายุ 36 - 50 ปี","netPremium":15358.56,"stamp":62,"vat":1079.44,"totalPremium":16500},{"quotationNumber":"Q15841X36G20240604T17303043324","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"X36","packageName":"งานประกันใหม่/ต่ออายุต่างบริษัท(เดิมX21) STD (6-10ปี) (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"1","repairType":"G","liability":{"tpbiPerPerson":"1000000","tpbiPerEvent":"10000000","tppdPerEvent":"5000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"280000","deductible":"0","tfSumInsured":"280000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"200000","passengerDeath":"200000","numberPassengerDeath":"6","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"200000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"อายุมากกว่า 50 ปี","netPremium":15358.56,"stamp":62,"vat":1079.44,"totalPremium":16500},{"quotationNumber":"Q15841EX2G20240604T17303043325","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"EX2","packageName":"แคมเปญพิเศษงานประกันใหม่(ป้ายดำ) อายุรถ2-10ปี เฉพาะรถเก๋งกลุ่ม5 (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"1","repairType":"G","liability":{"tpbiPerPerson":"1000000","tpbiPerEvent":"10000000","tppdPerEvent":"5000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"280000","deductible":"0","tfSumInsured":"280000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"200000","passengerDeath":"200000","numberPassengerDeath":"6","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"200000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":15823.85,"stamp":64,"vat":1112.15,"totalPremium":17000},{"quotationNumber":"Q15841X36G20240604T17303043326","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"X36","packageName":"งานประกันใหม่/ต่ออายุต่างบริษัท(เดิมX21) STD (6-10ปี) (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"1","repairType":"G","liability":{"tpbiPerPerson":"1000000","tpbiPerEvent":"10000000","tppdPerEvent":"5000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"280000","deductible":"0","tfSumInsured":"280000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"200000","passengerDeath":"200000","numberPassengerDeath":"6","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"200000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"อายุ 18 - 24 ปี","netPremium":15823.85,"stamp":64,"vat":1112.15,"totalPremium":17000},{"quotationNumber":"Q15841X36G20240604T17303043327","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"X36","packageName":"งานประกันใหม่/ต่ออายุต่างบริษัท(เดิมX21) STD (6-10ปี) (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"1","repairType":"G","liability":{"tpbiPerPerson":"1000000","tpbiPerEvent":"10000000","tppdPerEvent":"5000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"280000","deductible":"0","tfSumInsured":"280000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"200000","passengerDeath":"200000","numberPassengerDeath":"6","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"200000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"อายุ 25 - 35 ปี","netPremium":15823.85,"stamp":64,"vat":1112.15,"totalPremium":17000},{"quotationNumber":"Q15841X36G20240604T17303043328","carBrand":"HONDA","carModel":"CITY","carSubModel":"SV A","registrationYear":"2017","vehicleTypeCode":"110","engineCC":"1500","seat":7,"packageCode":"X36","packageName":"งานประกันใหม่/ต่ออายุต่างบริษัท(เดิมX21) STD (6-10ปี) (มีส่วนลด CCTV และ APP แล้ว)","insuranceType":"1","repairType":"G","liability":{"tpbiPerPerson":"1000000","tpbiPerEvent":"10000000","tppdPerEvent":"5000000","tppdDeductible":"0"},"ownDamage":{"sumInsured":"280000","deductible":"0","tfSumInsured":"280000"},"additionalNamesPeril":[{"title":"","value":""}],"additionalCoverage":{"personAccident":{"driverDeath":"200000","passengerDeath":"200000","numberPassengerDeath":"6","temporaryDisabilityDriver":"0","temporaryDisabilityPassenger":"0","numberPassengerTemporaryDisability":"0"},"medicalExpense":"200000","bailBond":"200000"},"terrorismExclusion":true,"ageRange":"ไม่ระบุ","netPremium":16289.14,"stamp":66,"vat":1144.86,"totalPremium":17500}]}
        ';

    }

}