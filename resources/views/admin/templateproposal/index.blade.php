@extends('layouts.admin')

@section('title', 'Template Proposal - Admin')
@section('page-title', 'Template Proposal')
@section('breadcrumb', 'Kelola template dokumen untuk ethical clearance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📄 Daftar Template Proposal</h5>
        <button class="btn btn-primary" onclick="alert('Fitur upload template akan segera tersedia')">
            <i class="fas fa-plus"></i> Upload Template
        </button>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Template</th>
                    <th>Deskripsi</th>
                    <th>Terakhir Update</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Belum ada template. Klik "Upload Template" untuk menambahkan.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection