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

use Addon\Models\RegistrationSpecialNeedModel;

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
        private StudyProgramModel $studyPrograms,
        private RegistrationSpecialNeedModel $specialNeeds
    ) {}

    private function convertDateToDb(?string $dateStr): ?string
    {
        if (empty($dateStr)) return null;
        $parts = explode('/', $dateStr);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $dateStr;
    }

    private function convertDateToUi(?string $dateStr): string
    {
        if (empty($dateStr)) return '';
        $timestamp = strtotime($dateStr);
        return $timestamp ? date('d/m/Y', $timestamp) : $dateStr;
    }

    public function showForm(Request $request, Response $response): View|RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);

        if ($registration && in_array($registration['status'], ['Verified', 'Released'])) {
            return $response->redirect('/dashboard?error=Pendaftaran+Anda+sudah+diverifikasi+dan+tidak+dapat+diubah.');
        }

        $regId = $registration ? $registration['id'] : null;

        if ($registration) {
            $registration['birth_date'] = $this->convertDateToUi($registration['birth_date'] ?? '');
        }

        $parents = $regId ? $this->parents->findByRegistrationId($regId) : null;
        if ($parents) {
            $parents['father_birth_date'] = $this->convertDateToUi($parents['father_birth_date'] ?? '');
            $parents['mother_birth_date'] = $this->convertDateToUi($parents['mother_birth_date'] ?? '');
            $parents['guardian_birth_date'] = $this->convertDateToUi($parents['guardian_birth_date'] ?? '');
        }

        $data = [
            'registration' => $registration,
            'address' => $regId ? $this->addresses->findByRegistrationId($regId) : null,
            'parents' => $parents,
            'special_needs' => $regId ? $this->specialNeeds->findByRegistrationId($regId) : null,
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

        if ($registration && in_array($registration['status'], ['Verified', 'Released'])) {
            $response->setStatusCode(403);
            return $response->json(['success' => false, 'message' => 'Pendaftaran sudah diverifikasi dan tidak dapat diubah']);
        }

        $regId = $registration ? $registration['id'] : null;

        if ($step === 1) {
            $fullName = $request->input('full_name');
            $birthPlace = $request->input('birth_place');
            $birthDate = $request->input('birth_date');
            $gender = $request->input('gender');
            $religion = $request->input('religion');
            $motherName = $request->input('mother_name');
            $infoSource = $request->input('info_source');

            if (!$fullName || !$birthPlace || !$birthDate || !$gender || !$religion || !$motherName || !$infoSource) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi semua kolom wajib pada data pribadi']);
            }

            $regData = [
                'user_id' => $userId,
                'full_name' => $fullName,
                'birth_place' => $birthPlace,
                'birth_date' => $this->convertDateToDb($birthDate),
                'gender' => $gender,
                'religion' => $religion,
                'mother_name' => $motherName,
                'info_source' => $infoSource,
                'status' => 'Draft'
            ];

            if ($regId) {
                $this->registrations->updateById($regId, $regData);
            } else {
                $regId = $this->registrations->insert($regData);
            }
        }

        if ($step === 2) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan data pribadi terlebih dahulu']);
            }

            $citizenship = $request->input('citizenship');
            $nik = $request->input('nik');
            $nisn = $request->input('nisn');
            $npwp = $request->input('npwp') ?: null;
            $street = $request->input('street') ?: null;
            $telephone = $request->input('telephone') ?: null;
            $dusun = $request->input('dusun') ?: null;
            $rt = $request->input('rt') ?: null;
            $rw = $request->input('rw') ?: null;
            $hp = $request->input('hp');
            $kelurahan = $request->input('subdistrict');
            $postalCode = $request->input('postal_code') ?: null;
            $email = $request->input('email');
            $kps_receiver = $request->input('kps_receiver');
            $kecamatan = $request->input('district');
            $transportation = $request->input('transportation') ?: null;
            $living_type = $request->input('living_type') ?: null;
            $province = $request->input('province') ?: null;
            $city = $request->input('city') ?: null;
            $address = $request->input('address') ?: null;

            if (!$citizenship || !$nik || !$nisn || !$hp || !$kelurahan || !$email || !$kps_receiver || !$kecamatan) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi semua kolom wajib pada alamat']);
            }

            if (!is_numeric($nik) || strlen($nik) !== 16) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'NIK harus berupa angka dan berjumlah 16 digit']);
            }

            if (!is_numeric($nisn)) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'NISN harus berupa angka']);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Format email tidak valid']);
            }

            if (!is_numeric($hp) || strlen($hp) < 9 || strlen($hp) > 15) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Nomor HP harus berupa angka dengan panjang 9-15 digit']);
            }

            if ($postalCode && (!is_numeric($postalCode) || strlen($postalCode) !== 5)) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Kode Pos harus berupa 5 digit angka']);
            }

            $this->registrations->updateById($regId, [
                'nik' => $nik,
                'nisn' => $nisn,
                'phone' => $hp,
                'email' => $email
            ]);

            $addrData = [
                'registration_id' => $regId,
                'citizenship' => $citizenship,
                'npwp' => $npwp,
                'street' => $street,
                'telephone' => $telephone,
                'dusun' => $dusun,
                'rt' => $rt,
                'rw' => $rw,
                'subdistrict' => $kelurahan,
                'kps_receiver' => $kps_receiver,
                'district' => $kecamatan,
                'transportation' => $transportation,
                'living_type' => $living_type,
                'postal_code' => $postalCode,
                'province' => $province,
                'city' => $city,
                'address' => $address
            ];

            $existingAddr = $this->addresses->findByRegistrationId($regId);
            if ($existingAddr) {
                $this->addresses->updateById($existingAddr['id'], $addrData);
            } else {
                $this->addresses->insert($addrData);
            }
        }

        if ($step === 3) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan langkah sebelumnya terlebih dahulu']);
            }

            $fatherName = $request->input('father_name');
            $fatherNik = $request->input('father_nik');
            $fatherBirthDate = $request->input('father_birth_date');
            $fatherEducation = $request->input('father_education');
            $fatherOccupation = $request->input('father_occupation');
            $fatherIncome = $request->input('father_income');

            $motherName = $request->input('parent_mother_name') ?: $request->input('mother_name');
            $motherNik = $request->input('mother_nik');
            $motherBirthDate = $request->input('mother_birth_date');
            $motherEducation = $request->input('mother_education');
            $motherOccupation = $request->input('mother_occupation');
            $motherIncome = $request->input('mother_income');

            $guardianName = $request->input('guardian_name');
            $guardianBirthDate = $request->input('guardian_birth_date');
            $guardianEducation = $request->input('guardian_education');
            $guardianOccupation = $request->input('guardian_occupation');
            $guardianIncome = $request->input('guardian_income');

            $isParentFilled = !empty($fatherName) || !empty($motherName);

            if ($isParentFilled) {
                if (!$fatherName || !$fatherNik || !$fatherBirthDate || !$fatherEducation || !$fatherOccupation || !$fatherIncome ||
                    !$motherName || !$motherNik || !$motherBirthDate || !$motherEducation || !$motherOccupation || !$motherIncome) {
                    $response->setStatusCode(400);
                    return $response->json(['success' => false, 'message' => 'Harap lengkapi semua kolom data Orang Tua (Ayah dan Ibu)']);
                }

                if (!is_numeric($fatherNik) || strlen($fatherNik) !== 16 || !is_numeric($motherNik) || strlen($motherNik) !== 16) {
                    $response->setStatusCode(400);
                    return $response->json(['success' => false, 'message' => 'NIK orang tua harus berupa 16 digit angka']);
                }
            } else {
                if (!$guardianName || !$guardianBirthDate || !$guardianEducation || !$guardianOccupation || !$guardianIncome) {
                    $response->setStatusCode(400);
                    return $response->json(['success' => false, 'message' => 'Jika Orang Tua tidak diisi, maka semua kolom data Wali wajib diisi']);
                }
            }

            $parentData = [
                'registration_id' => $regId,
                'father_name' => $fatherName ?: null,
                'father_nik' => $fatherNik ?: null,
                'father_birth_date' => $fatherBirthDate ? $this->convertDateToDb($fatherBirthDate) : null,
                'father_education' => $fatherEducation ?: null,
                'father_occupation' => $fatherOccupation ?: null,
                'father_income' => $fatherIncome ?: null,
                'mother_name' => $motherName ?: null,
                'mother_nik' => $motherNik ?: null,
                'mother_birth_date' => $motherBirthDate ? $this->convertDateToDb($motherBirthDate) : null,
                'mother_education' => $motherEducation ?: null,
                'mother_occupation' => $motherOccupation ?: null,
                'mother_income' => $motherIncome ?: null,
                'guardian_name' => $guardianName ?: null,
                'guardian_birth_date' => $guardianBirthDate ? $this->convertDateToDb($guardianBirthDate) : null,
                'guardian_education' => $guardianEducation ?: null,
                'guardian_occupation' => $guardianOccupation ?: null,
                'guardian_income' => $guardianIncome ?: null
            ];

            $existingParents = $this->parents->findByRegistrationId($regId);
            if ($existingParents) {
                $this->parents->updateById($existingParents['id'], $parentData);
            } else {
                $this->parents->insert($parentData);
            }
        }

        if ($step === 4) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan langkah sebelumnya terlebih dahulu']);
            }

            $hasSpecialNeeds = $request->input('has_special_needs');
            $studentNeeds = $request->input('student_needs') ?: [];
            $fatherNeeds = $request->input('father_needs') ?: [];
            $motherNeeds = $request->input('mother_needs') ?: [];
            $guardianNeeds = $request->input('guardian_needs') ?: [];

            if (!$hasSpecialNeeds) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Pilihan Kebutuhan Khusus wajib diisi']);
            }

            $needsData = [
                'registration_id' => $regId,
                'has_special_needs' => $hasSpecialNeeds,
                'student_needs' => json_encode($studentNeeds),
                'father_needs' => json_encode($fatherNeeds),
                'mother_needs' => json_encode($motherNeeds),
                'guardian_needs' => json_encode($guardianNeeds)
            ];

            $existingNeeds = $this->specialNeeds->findByRegistrationId($regId);
            if ($existingNeeds) {
                $this->specialNeeds->updateById($existingNeeds['id'], $needsData);
            } else {
                $this->specialNeeds->insert($needsData);
            }
        }

        if ($step === 5) {
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

        if ($step === 6) {
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

        return $response->json(['success' => true, 'message' => 'Draft berhasil disimpan', 'registration_id' => $regId]);
    }

    public function submit(Request $request, Response $response): RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);

        if (!$registration || in_array($registration['status'], ['Verified', 'Released'])) {
            return $response->redirect('/dashboard?error=Pendaftaran+tidak+valid+atau+sudah+dikunci');
        }

        $regId = $registration['id'];

        $existingProg = $this->programs->findByRegistrationId($regId);

        $ayId = $request->input('academic_year_id') ?: ($registration['academic_year_id'] ?? null);
        $waveId = $request->input('wave_id') ?: ($registration['wave_id'] ?? null);
        $pathId = $request->input('admission_path_id') ?: ($registration['admission_path_id'] ?? null);
        $classId = $request->input('class_id') ?: ($registration['class_id'] ?? null);
        $prog1Id = $request->input('program1_id') ?: ($existingProg['program1_id'] ?? null);
        $prog2Id = $request->input('program2_id') ?: ($existingProg['program2_id'] ?? null);

        if (!$ayId || !$waveId || !$pathId || !$classId || !$prog1Id) {
            return $response->redirect('/pendaftaran?error=' . urlencode('Harap lengkapi semua pilihan PMB dan program studi wajib sebelum memfinalisasi pendaftaran.'));
        }

        if ($prog1Id && $prog2Id && $prog1Id == $prog2Id) {
            return $response->redirect('/pendaftaran?error=' . urlencode('Pilihan program studi 1 dan program studi 2 tidak boleh sama.'));
        }

        $this->registrations->updateById($regId, [
            'academic_year_id' => $ayId,
            'wave_id' => $waveId,
            'admission_path_id' => $pathId,
            'class_id' => $classId,
            'status' => 'Submitted'
        ]);

        $progData = [
            'registration_id' => $regId,
            'program1_id' => $prog1Id,
            'program2_id' => $prog2Id
        ];

        if ($existingProg) {
            $this->programs->updateById($existingProg['id'], $progData);
        } else {
            $this->programs->insert($progData);
        }

        return $response->redirect('/dashboard?success=Pendaftaran+berhasil+dikunci.+Panitia+akan+segera+memverifikasi+berkas+Anda.');
    }
}
