<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationAddressModel;
use Addon\Models\RegistrationParentModel;
use Addon\Models\RegistrationEducationModel;
use Addon\Models\RegistrationProgramModel;
use Addon\Models\AcademicYearModel;
use Addon\Models\WaveModel;
use Addon\Models\AdmissionPathModel;
use Addon\Models\ClassModel;
use Addon\Models\StudyProgramModel;

class RegistrationController
{
    public function __construct(
        private RegistrationModel $registrations,
        private RegistrationAddressModel $addresses,
        private RegistrationParentModel $parents,
        private RegistrationEducationModel $educations,
        private RegistrationProgramModel $programs,
        private AcademicYearModel $academicYears,
        private WaveModel $waves,
        private AdmissionPathModel $admissionPaths,
        private ClassModel $classes,
        private StudyProgramModel $studyPrograms
    ) {}

    public function showForm(Request $request, Response $response): View|RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);

        if ($registration && $registration['status'] !== 'Draft') {
            return $response->redirect('/dashboard?error=Pendaftaran+Anda+sudah+dikunci+dan+tidak+dapat+diubah.');
        }

        $regId = $registration ? $registration['id'] : null;

        $data = [
            'registration' => $registration,
            'address' => $regId ? $this->addresses->findByRegistrationId($regId) : null,
            'parents' => $regId ? $this->parents->findByRegistrationId($regId) : null,
            'education' => $regId ? $this->educations->findByRegistrationId($regId) : null,
            'program' => $regId ? $this->programs->findByRegistrationId($regId) : null,
            
            'academic_years' => $this->academicYears->all(),
            'waves' => $this->waves->all(),
            'admission_paths' => $this->admissionPaths->all(),
            'classes' => $this->classes->all(),
            'study_programs' => $this->studyPrograms->all(),
        ];

        return $response->renderPage($data, [
            'path' => '/pendaftaran/form',
            'meta' => ['title' => 'Formulir Pendaftaran PMB | ' . env('APP_NAME')]
        ]);
    }

    public function saveDraft(Request $request, Response $response): Response
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            return $response->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $step = (int) $request->input('step');
        $registration = $this->registrations->findByUserId($userId);

        if ($registration && $registration['status'] !== 'Draft') {
            $response->setStatusCode(403);
            return $response->json(['success' => false, 'message' => 'Pendaftaran sudah difinalisasi']);
        }

        $regId = $registration ? $registration['id'] : null;

        // Step 1: Data Pribadi
        if ($step === 1) {
            $fullName = $request->input('full_name');
            $nik = $request->input('nik');
            $nisn = $request->input('nisn');
            $birthPlace = $request->input('birth_place');
            $birthDate = $request->input('birth_date');
            $gender = $request->input('gender');
            $religion = $request->input('religion');
            $email = $request->input('email');
            $phone = $request->input('phone');

            if (!$fullName || !$nik || !$nisn || !$birthPlace || !$birthDate || !$gender || !$religion || !$email || !$phone) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi semua kolom wajib pada data pribadi']);
            }

            if (!is_numeric($nik) || strlen($nik) !== 16) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'NIK harus berupa angka dan berjumlah 16 digit']);
            }

            if (!is_numeric($nisn) || strlen($nisn) !== 10) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'NISN harus berupa angka dan berjumlah 10 digit']);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Format email tidak valid']);
            }

            if (!is_numeric($phone) || strlen($phone) < 9 || strlen($phone) > 15) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Nomor telepon harus berupa angka dengan panjang 9-15 digit']);
            }

            $regData = [
                'user_id' => $userId,
                'full_name' => $fullName,
                'nik' => $nik,
                'nisn' => $nisn,
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'religion' => $religion,
                'email' => $email,
                'phone' => $phone,
                'status' => 'Draft'
            ];

            if ($regId) {
                $this->registrations->updateById($regId, $regData);
            } else {
                $regId = $this->registrations->insert($regData);
            }
        }

        // Step 2: Alamat
        if ($step === 2) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan data pribadi terlebih dahulu']);
            }

            $province = $request->input('province');
            $city = $request->input('city');
            $district = $request->input('district');
            $subdistrict = $request->input('subdistrict');
            $postalCode = $request->input('postal_code');
            $address = $request->input('address');

            if (!$province || !$city || !$district || !$subdistrict || !$postalCode || !$address) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi semua kolom alamat lengkap']);
            }

            if (!is_numeric($postalCode) || strlen($postalCode) !== 5) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Kode Pos harus berupa angka dan berjumlah 5 digit']);
            }

            $addrData = [
                'registration_id' => $regId,
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'subdistrict' => $subdistrict,
                'postal_code' => $postalCode,
                'address' => $address
            ];

            $existingAddr = $this->addresses->findByRegistrationId($regId);
            if ($existingAddr) {
                $this->addresses->updateById($existingAddr['id'], $addrData);
            } else {
                $this->addresses->insert($addrData);
            }
        }

        // Step 3: Orang Tua
        if ($step === 3) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan langkah sebelumnya terlebih dahulu']);
            }

            $fatherName = $request->input('father_name');
            $fatherEducation = $request->input('father_education');
            $fatherOccupation = $request->input('father_occupation');
            $fatherIncome = $request->input('father_income');

            $motherName = $request->input('mother_name');
            $motherEducation = $request->input('mother_education');
            $motherOccupation = $request->input('mother_occupation');
            $motherIncome = $request->input('mother_income');

            $guardianName = $request->input('guardian_name') ?: null;
            $guardianEducation = $request->input('guardian_education') ?: null;
            $guardianOccupation = $request->input('guardian_occupation') ?: null;
            $guardianIncome = $request->input('guardian_income') ?: null;

            if (!$fatherName || !$fatherEducation || !$fatherOccupation || !$fatherIncome || !$motherName || !$motherEducation || !$motherOccupation || !$motherIncome) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi kolom data ayah & ibu']);
            }

            $parentData = [
                'registration_id' => $regId,
                'father_name' => $fatherName,
                'father_education' => $fatherEducation,
                'father_occupation' => $fatherOccupation,
                'father_income' => $fatherIncome,
                'mother_name' => $motherName,
                'mother_education' => $motherEducation,
                'mother_occupation' => $motherOccupation,
                'mother_income' => $motherIncome,
                'guardian_name' => $guardianName,
                'guardian_education' => $guardianEducation,
                'guardian_occupation' => $guardianOccupation,
                'guardian_income' => $guardianIncome
            ];

            $existingParents = $this->parents->findByRegistrationId($regId);
            if ($existingParents) {
                $this->parents->updateById($existingParents['id'], $parentData);
            } else {
                $this->parents->insert($parentData);
            }
        }

        // Step 4: Pendidikan
        if ($step === 4) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan langkah sebelumnya terlebih dahulu']);
            }

            $schoolName = $request->input('school_name');
            $schoolMajor = $request->input('school_major');
            $graduationYear = $request->input('graduation_year');
            $diplomaNumber = $request->input('diploma_number');
            $averageScore = $request->input('average_score');

            if (!$schoolName || !$schoolMajor || !$graduationYear || !$diplomaNumber || !$averageScore) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi semua kolom riwayat pendidikan']);
            }

            if (!is_numeric($graduationYear)) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Tahun lulus harus berupa angka']);
            }

            if (!is_numeric($averageScore) || $averageScore < 0 || $averageScore > 100) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Rata-rata nilai rapor/ijazah harus berupa angka antara 0 sampai 100']);
            }

            $eduData = [
                'registration_id' => $regId,
                'school_name' => $schoolName,
                'school_major' => $schoolMajor,
                'graduation_year' => $graduationYear,
                'diploma_number' => $diplomaNumber,
                'average_score' => $averageScore
            ];

            $existingEdu = $this->educations->findByRegistrationId($regId);
            if ($existingEdu) {
                $this->educations->updateById($existingEdu['id'], $eduData);
            } else {
                $this->educations->insert($eduData);
            }
        }

        // Step 5: Pilihan PMB & Simpan
        if ($step === 5) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan langkah sebelumnya terlebih dahulu']);
            }

            $ayId = $request->input('academic_year_id');
            $waveId = $request->input('wave_id');
            $pathId = $request->input('admission_path_id');
            $classId = $request->input('class_id');
            $prog1Id = $request->input('program1_id');
            $prog2Id = $request->input('program2_id') ?: null;

            if (!$ayId || !$waveId || !$pathId || !$classId || !$prog1Id) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap lengkapi semua pilihan PMB wajib']);
            }

            if ($prog1Id == $prog2Id) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Pilihan program studi 1 dan program studi 2 tidak boleh sama']);
            }

            // Update registration basic fields
            $this->registrations->updateById($regId, [
                'academic_year_id' => $ayId,
                'wave_id' => $waveId,
                'admission_path_id' => $pathId,
                'class_id' => $classId
            ]);

            // Save programs relation
            $progData = [
                'registration_id' => $regId,
                'program1_id' => $prog1Id,
                'program2_id' => $prog2Id
            ];

            $existingProg = $this->programs->findByRegistrationId($regId);
            if ($existingProg) {
                $this->programs->updateById($existingProg['id'], $progData);
            } else {
                $this->programs->insert($progData);
            }
        }

        return $response->json(['success' => true, 'message' => 'Draft berhasil disimpan', 'registration_id' => $regId]);
    }

    public function submit(Request $request, Response $response): RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);

        if (!$registration || $registration['status'] !== 'Draft') {
            return $response->redirect('/dashboard?error=Pendaftaran+tidak+valid+atau+sudah+dikunci');
        }

        $regId = $registration['id'];

        $ayId = $request->input('academic_year_id');
        $waveId = $request->input('wave_id');
        $pathId = $request->input('admission_path_id');
        $classId = $request->input('class_id');
        $prog1Id = $request->input('program1_id');
        $prog2Id = $request->input('program2_id') ?: null;

        if ($ayId && $waveId && $pathId && $classId && $prog1Id) {
            $this->registrations->updateById($regId, [
                'academic_year_id' => $ayId,
                'wave_id' => $waveId,
                'admission_path_id' => $pathId,
                'class_id' => $classId
            ]);

            $progData = [
                'registration_id' => $regId,
                'program1_id' => $prog1Id,
                'program2_id' => $prog2Id
            ];

            $existingProg = $this->programs->findByRegistrationId($regId);
            if ($existingProg) {
                $this->programs->updateById($existingProg['id'], $progData);
            } else {
                $this->programs->insert($progData);
            }
        }

        $this->registrations->updateById($registration['id'], [
            'status' => 'Submitted'
        ]);

        return $response->redirect('/dashboard?success=Pendaftaran+berhasil+dikunci.+Panitia+akan+segera+memverifikasi+berkas+Anda.');
    }
}
