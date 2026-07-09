<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\AcademicYearModel;
use Addon\Models\WaveModel;
use Addon\Models\FacultyModel;
use Addon\Models\StudyProgramModel;
use Addon\Models\AdmissionPathModel;
use Addon\Models\ClassModel;
use Addon\Models\SelectionRoomModel;
use Addon\Models\DocumentTypeModel;

class MasterController
{
    public function __construct(
        private AcademicYearModel $academicYears,
        private WaveModel $waves,
        private FacultyModel $faculties,
        private StudyProgramModel $studyPrograms,
        private AdmissionPathModel $admissionPaths,
        private ClassModel $classes,
        private SelectionRoomModel $selectionRooms,
        private DocumentTypeModel $documentTypes
    ) {}

    public function index(Request $request, Response $response): View
    {
        $tab = $request->input('tab') ?? 'academic-year';

        $data = [
            'tab' => $tab,
            'academic_years' => $this->academicYears->all(),
            'waves' => $this->waves->all(),
            'faculties' => $this->faculties->all(),
            'study_programs' => $this->studyPrograms->all(),
            'admission_paths' => $this->admissionPaths->all(),
            'classes' => $this->classes->all(),
            'selection_rooms' => $this->selectionRooms->all(),
            'document_types' => $this->documentTypes->all(),
        ];

        return $response->renderPage($data, [
            'path' => '/admin/master',
            'meta' => ['title' => 'Master Data Control Center | ' . env('APP_NAME')]
        ]);
    }

    public function create(Request $request, Response $response): RedirectResponse
    {
        $type = $request->input('type');

        switch ($type) {
            case 'academic-year':
                $year = $request->input('year');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$year) return $response->redirect('/admin/master?tab=academic-year&error=Tahun+akademik+harus+diisi');
                $this->academicYears->insert(['year' => $year, 'is_active' => $is_active]);
                break;

            case 'wave':
                $name = $request->input('name');
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name || !$start || !$end) return $response->redirect('/admin/master?tab=wave&error=Seluruh+kolom+harus+diisi');
                $this->waves->insert(['name' => $name, 'start_date' => $start, 'end_date' => $end, 'is_active' => $is_active]);
                break;

            case 'faculty':
                $code = $request->input('code');
                $name = $request->input('name');
                if (!$code || !$name) return $response->redirect('/admin/master?tab=faculty&error=Kode+dan+nama+fakultas+harus+diisi');
                $this->faculties->insert(['code' => strtoupper($code), 'name' => $name]);
                break;

            case 'study-program':
                $faculty_id = $request->input('faculty_id');
                $code = $request->input('code');
                $name = $request->input('name');
                if (!$faculty_id || !$code || !$name) return $response->redirect('/admin/master?tab=study-program&error=Seluruh+kolom+harus+diisi');
                $this->studyPrograms->insert(['faculty_id' => $faculty_id, 'code' => strtoupper($code), 'name' => $name]);
                break;

            case 'admission-path':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name) return $response->redirect('/admin/master?tab=admission-path&error=Nama+jalur+harus+diisi');
                $this->admissionPaths->insert(['name' => $name, 'is_active' => $is_active]);
                break;

            case 'class':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name) return $response->redirect('/admin/master?tab=class&error=Nama+kelas+harus+diisi');
                $this->classes->insert(['name' => $name, 'is_active' => $is_active]);
                break;

            case 'selection-room':
                $name = $request->input('name');
                $location = $request->input('location');
                if (!$name || !$location) return $response->redirect('/admin/master?tab=selection-room&error=Nama+dan+lokasi+harus+diisi');
                $this->selectionRooms->insert(['name' => $name, 'location' => $location]);
                break;

            case 'document-type':
                $name = $request->input('name');
                $is_required = $request->input('is_required') ? 1 : 0;
                if (!$name) return $response->redirect('/admin/master?tab=document-type&error=Nama+dokumen+harus+diisi');
                $this->documentTypes->insert(['name' => $name, 'is_required' => $is_required]);
                break;
        }

        log_activity('CREATE_MASTER_DATA', "Menambahkan data master '{$type}' baru.");
        return $response->redirect("/admin/master?tab={$type}&success=Data+berhasil+ditambahkan");
    }

    public function update(Request $request, Response $response): RedirectResponse
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if (!$id) return $response->redirect("/admin/master?tab={$type}&error=ID+tidak+valid");

        switch ($type) {
            case 'academic-year':
                $year = $request->input('year');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->academicYears->updateById($id, ['year' => $year, 'is_active' => $is_active]);
                break;

            case 'wave':
                $name = $request->input('name');
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->waves->updateById($id, ['name' => $name, 'start_date' => $start, 'end_date' => $end, 'is_active' => $is_active]);
                break;

            case 'faculty':
                $code = $request->input('code');
                $name = $request->input('name');
                $this->faculties->updateById($id, ['code' => strtoupper($code), 'name' => $name]);
                break;

            case 'study-program':
                $faculty_id = $request->input('faculty_id');
                $code = $request->input('code');
                $name = $request->input('name');
                $this->studyPrograms->updateById($id, ['faculty_id' => $faculty_id, 'code' => strtoupper($code), 'name' => $name]);
                break;

            case 'admission-path':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->admissionPaths->updateById($id, ['name' => $name, 'is_active' => $is_active]);
                break;

            case 'class':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->classes->updateById($id, ['name' => $name, 'is_active' => $is_active]);
                break;

            case 'selection-room':
                $name = $request->input('name');
                $location = $request->input('location');
                $this->selectionRooms->updateById($id, ['name' => $name, 'location' => $location]);
                break;

            case 'document-type':
                $name = $request->input('name');
                $is_required = $request->input('is_required') ? 1 : 0;
                $this->documentTypes->updateById($id, ['name' => $name, 'is_required' => $is_required]);
                break;
        }

        log_activity('UPDATE_MASTER_DATA', "Memperbarui data master '{$type}' dengan ID {$id}.");
        return $response->redirect("/admin/master?tab={$type}&success=Data+berhasil+diperbarui");
    }

    public function delete(Request $request, Response $response): RedirectResponse
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if (!$id) return $response->redirect("/admin/master?tab={$type}&error=ID+tidak+valid");

        switch ($type) {
            case 'academic-year':
                $this->academicYears->deleteById($id);
                break;
            case 'wave':
                $this->waves->deleteById($id);
                break;
            case 'faculty':
                $this->faculties->deleteById($id);
                break;
            case 'study-program':
                $this->studyPrograms->deleteById($id);
                break;
            case 'admission-path':
                $this->admissionPaths->deleteById($id);
                break;
            case 'class':
                $this->classes->deleteById($id);
                break;
            case 'selection-room':
                $this->selectionRooms->deleteById($id);
                break;
            case 'document-type':
                $this->documentTypes->deleteById($id);
                break;
        }

        log_activity('DELETE_MASTER_DATA', "Menghapus data master '{$type}' dengan ID {$id}.");
        return $response->redirect("/admin/master?tab={$type}&success=Data+berhasil+dihapus");
    }
}
