<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CarInsuranceQuotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Use $this['key'] syntax to access array elements reliably.
        return [
            'insuranceCompany' => 'Viriyah',
            'insuranclogo' => asset('/public/admin/insurance-logo/viriyah.webp'),
            'quotationNumber' => $this['quotationNumber'] ?? '',
            'carBrand' => $this['carBrand'] ?? '',
            'carModel' => $this['carModel'] ?? '',
            'carSubModel' => $this['carSubModel'] ?? '',
            'registrationYear' => $this['registrationYear'] ?? '',
            'vehicleTypeCode' => $this['vehicleTypeCode'] ?? '',
            'engineCC' => $this['engineCC'] ?? '',
            'seat' => (int) ($this['seat'] ?? 0),
            'packageCode' => $this['packageCode'] ?? '',
            'packageName' => $this['packageName'] ?? '',
            'insuranceType' => $this['insuranceType'] ?? '',
            'repairType' => $this['repairType'] ?? '',
            'liability' => [
                // Access nested array elements directly
                'tpbiPerPerson' => $this['liability']['tpbiPerPerson'] ?? '0',
                'tpbiPerEvent' => $this['liability']['tpbiPerEvent'] ?? '0',
                'tppdPerEvent' => $this['liability']['tppdPerEvent'] ?? '0',
                'tppdDeductible' => $this['liability']['tppdDeductible'] ?? '0',
            ],
            'ownDamage' => [
                'sumInsured' => $this['ownDamage']['sumInsured'] ?? '0',
                'deductible' => $this['ownDamage']['deductible'] ?? '0',
                'tfSumInsured' => $this['ownDamage']['tfSumInsured'] ?? '0',
            ],
            'additionalNamesPeril' => $this['additionalNamesPeril'] ?? [],
            'additionalCoverage' => [
                'personAccident' => [
                    'driverDeath' => $this['additionalCoverage']['personAccident']['driverDeath'] ?? '0',
                    'passengerDeath' => $this['additionalCoverage']['personAccident']['passengerDeath'] ?? '0',
                    'numberPassengerDeath' => $this['additionalCoverage']['personAccident']['numberPassengerDeath'] ?? '0',
                    'temporaryDisabilityDriver' => $this['additionalCoverage']['personAccident']['temporaryDisabilityDriver'] ?? '0',
                    'temporaryDisabilityPassenger' => $this['additionalCoverage']['personAccident']['temporaryDisabilityPassenger'] ?? '0',
                    'numberPassengerTemporaryDisability' => $this['additionalCoverage']['personAccident']['numberPassengerTemporaryDisability'] ?? '0',
                ],
                'medicalExpense' => $this['additionalCoverage']['medicalExpense'] ?? '0',
                'bailBond' => $this['additionalCoverage']['bailBond'] ?? '0',
            ],
            'terrorismExclusion' => (bool) ($this['terrorismExclusion'] ?? false),
            'ageRange' => $this['ageRange'] ?? '',
            'netPremium' => (float) ($this['netPremium'] ?? 0.0),
            'stamp' => (float) ($this['stamp'] ?? 0.0),
            'vat' => (float) ($this['vat'] ?? 0.0),
            'totalPremium' => (float) ($this['totalPremium'] ?? 0.0),
        ];
    }
}
