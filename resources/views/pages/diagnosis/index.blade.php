@extends('layouts.app')
@section('content')
    <div class="w-full bg-white">
        <div class="space-y-8">
            <div class="head">
                <h2 class="text-3xl font-bold">Data Diagnosa</h2>
            </div>
            <div class="line h-2 rounded-full w-full bg-theme-border-sidebar/20">
                <div class="line h-2 rounded-full w-2/4 bg-theme-border-sidebar"></div>
            </div>
        </div>
        <div class="mt-5 max-w-2xl space-y-5">
            <form action="#" id="diagnosaForm" method="POST">
                @csrf
                <x-forms.input name="code_diagnosis" maxlength="6" type="text" id="code_diagnosis" label="Kode Diagnosa" />
                <x-forms.input name="name" type="text" id="name" label="Diagnosa" />
                <button type="submit" name="create_record" id="create_record" class="px-4 py-3 text-white rounded-lg bg-theme-border-sidebar text-center mt-5">Simpan<span class="ml-4 mt-4"></button>
            </form>
        </div>
        <div class="shadow border p-5 mt-20 bg-white">
            <div class="p-2 lg:flex grid space-y-5 grid-cols-1 justify-between">
                <div class="mt-3 w-full">
                    <h2 class="text-2xl font-bold">Table Diagnosa</h2>
                </div>
                <div class="w-full flex justify-end gap-5">
                    {{-- <a href="#" class="px-7 py-3 text-white rounded-lg bg-theme-border-sidebar">Batal<span class="ml-4 mt-4"></span></a>
                    <a href="#" class="px-7 py-3 text-white rounded-lg bg-theme-border-sidebar">Insert File<span class="ml-4 mt-4"></span></a> --}}
                </div>
            </div>
            <div class="flex justify-end">
                <div class="input-box-search flex mt-5 border">
                    <button class="px-3 py-2 text-lg"><iconify-icon icon="clarity:search-line"></iconify-icon></button>
                    <input type="search" placeholder="Search" class="p-2 outline-none w-full">
                </div>
            </div>
            {{-- Table --}}
             <div class="tables-responsive overflow-y-auto mt-10">
                <table class="table table-bordered table-striped" id="diagnosa_table">
                    <thead>
                        <tr>
                            <th width="10%">No.</th>
                            <th width="35%">Kode Diagnosa</th>
                            <th width="35%">Diagnosa</th>
                            <th width="30%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagnosis as $diagnosa)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $diagnosa->code_diagnosis }}</td>
                            <td>{{ $diagnosa->name }}</td>
                            <td>
                                <button class="inline-flex items-center px-4 py-2 rounded-lg bg-theme-border-sidebar text-white text-sm md:text-left font-medium" id="{{ $diagnosa->id }}">Edit</button>

                                <button class="inline-flex  items-center px-4 py-2 rounded-lg bg-theme-border-sidebar/20 text-theme-border-sidebar text-sm md:text-left font-medium" onclick="confirmDelete({{ $diagnosa['id'] }})" id="{{ $diagnosa->id }}" >Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection

@push('script-injection')

<script>
    $(document).ready(function() {
    // CREATE
    $('#diagnosaForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: "{{ route('admin.diagnosis.store') }}",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                window.location.reload();
                // Update table or show success message
            },
            error: function(error) {
                // Show error message
            }
        });
    });

    // READ
    function load_data() {
        $.ajax({
            url: "{{ route('admin.diagnosis.index') }}",
            method: "GET",
            dataType: "json",
            success: function(data) {
                // Populate table with data
            }
        });
    }
    load_data();

    // UPDATE
    $(document).on('click', '.update', function() {
        var id = $(this).attr('id');
        $.ajax({
            url: "/admin/diagnosis/"+id+"/edit",
            method: "GET",
            dataType: "json",
            success: function(data) {
                res = data.data;
                // Populate form with data for editing
                var code_diagnosis = $('#code_diagnosis').val(res.code_diagnosis);
                code_diagnosis.attr('readonly', true);
                var name = $('#name').val(res.name);
            }
        });
    });


    // DELETE
    $(document).on('click', '.delete', function() {
        var id = $(this).attr('id');
        if (confirm("Apakah anda yakin ingin menghapus data ini?")) {
            $.ajax({
                url: "/admin/diagnosis/"+id,
                method: "DELETE",
                data: {_token: $('input[name="_token"]').val()},
                dataType: "json",
                success: function(data) {
                    window.location.reload();
                    // Remove data from table or show success message
                },
                error: function(error) {
                    // Show error message
                    console.log(error);
                }
            });
        }
    });
});

function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if(!result.isConfirmed) return;

            const formElement = `<form action="{{ route('admin.diagnosis.index') }}/${id}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>`;

            $(formElement).appendTo('body').submit();
        });
    }

</script>

@endpush
