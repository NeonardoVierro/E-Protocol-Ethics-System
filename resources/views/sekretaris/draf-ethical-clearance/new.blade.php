@extends('layouts.sekretaris')

@section('title', 'Draft Ethical Clearance')
@section('page-title', 'Draft Ethical Clearance')
@section('breadcrumb', 'Draft Ethical Clearance')

@section('content')
<div>
  <div class="max-w-container-max mx-auto">
    <div class="mb-8">
      <h2 class="font-h2 text-h2 text-on-surface mb-1">Draft Ethical Clearance</h2>
      <p class="font-body-md text-on-surface-variant">Generate dan tinjau draft sertifikat kelaikan etik untuk proposal yang telah disetujui.</p>
    </div>

    <style>
      /* quick view-scoped helpers for selection and hover */
      .proposal-selected { border-color: #1d4ed8; background-color: #eff6ff; box-shadow: 0 6px 18px -8px rgba(29,78,216,0.25); }
      .proposal-item { transition: all .15s ease; }
      .btn-hoverable:hover { transform: translateY(-1px); box-shadow: 0 6px 18px -10px rgba(0,0,0,0.08); }
      .control-hoverable:hover { box-shadow: 0 6px 18px -10px rgba(0,0,0,0.04); }
    </style>

    <div class="grid grid-cols-12 gap-gutter">
      <div class="col-span-12 lg:col-span-4 space-y-6">
        <div class="bg-white border border-outline-variant rounded-xl shadow-sm p-lg">
          <div class="flex items-center justify-between mb-md">
            <h3 class="font-h3 text-h3">Antrean Draft</h3>
            <span class="bg-secondary-container text-on-secondary-container px-2 py-1 rounded text-label-caps uppercase">{{ count($approvedProposals ?? []) }} Pending</span>
          </div>
          <div class="space-y-4">
            @foreach($approvedProposals as $p)
              <div class="p-md rounded-lg border border-outline-variant bg-white hover:border-primary-fixed-dim transition-colors cursor-pointer group proposal-item" data-proposal='@json($p)'>
                <div class="flex justify-between items-start mb-2">
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ID: EP-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span>
                  <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold">APPROVED</span>
                </div>
                <p class="text-body-md font-semibold text-on-surface line-clamp-2 mb-1 group-hover:text-primary transition-colors">{{ Str::limit($p->title, 120) }}</p>
                <p class="text-body-sm text-on-surface-variant flex items-center">
                  <span class="material-symbols-outlined text-[14px] mr-1">person</span>
                  {{ $p->nama_peneliti ?? $p->researcher?->name ?? '—' }}
                </p>
              </div>
            @endforeach

            @if(empty($approvedProposals) || $approvedProposals->isEmpty())
              <p class="text-sm text-slate-500">Tidak ada proposal approved untuk saat ini.</p>
            @endif
          </div>
        </div>

        <div class="bg-primary-container text-on-primary-container p-lg rounded-xl shadow-lg relative overflow-hidden">
          <div class="relative z-10">
            <p class="text-label-caps opacity-80 mb-1">PROSES BULAN INI</p>
            <p class="text-[32px] font-bold">{{ $drafts ? count($drafts) : 0 }}</p>
            <p class="text-body-sm opacity-90 mt-2">Draft yang dibuat oleh sekretariat.</p>
          </div>
          <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[120px] opacity-10">verified</span>
        </div>
      </div>

      <div class="col-span-12 lg:col-span-8 space-y-6">
        <form id="draftForm" action="{{ route('sekretaris.draf-ethical-clearance.store') }}" method="POST">
          @csrf
          <div class="bg-white border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="px-lg py-md bg-slate-50 border-b border-outline-variant flex items-center justify-between">
              <h3 class="font-h3 text-[16px] text-blue-900">Data Sertifikat</h3>
              <span class="text-[11px] font-medium text-slate-500 italic">Data ini akan dipopulasikan otomatis ke dalam draft (tanggal dikosongkan).</span>
            </div>

            <div class="p-lg grid grid-cols-2 gap-md">
              <input type="hidden" id="proposalIdInput" name="proposal_id" value="" />

              <div class="col-span-2">
                <label class="block text-label-caps text-on-surface-variant mb-2">Project Title</label>
                <textarea id="titleInput" name="title" class="w-full px-4 py-2 bg-slate-50 border border-outline-variant rounded-lg text-body-md control-hoverable focus:ring-1 focus:border-primary transition" rows="2">{{ old('title') }}</textarea>
              </div>

              <div class="col-span-2">
                <label class="block text-label-caps text-on-surface-variant mb-2">Ketua Peneliti</label>
                <input id="piInput" name="principal_investigator" class="w-full px-4 py-2 bg-slate-50 border border-outline-variant rounded-lg text-body-md control-hoverable focus:ring-1 focus:border-primary transition" value="{{ old('principal_investigator') }}" />
              </div>

              <div class="col-span-2">
                <label class="block text-label-caps text-on-surface-variant mb-2">Anggota Peneliti (pisahkan baris)</label>
                <textarea id="membersInput" name="members" class="w-full px-4 py-2 bg-slate-50 border border-outline-variant rounded-lg text-body-md control-hoverable focus:ring-1 focus:border-primary transition" rows="3">{{ old('members') }}</textarea>
              </div>

              <div class="col-span-1">
                <label class="block text-label-caps text-on-surface-variant mb-2">Institusi</label>
                <input id="institutionInput" name="institution" class="w-full px-4 py-2 bg-slate-50 border border-outline-variant rounded-lg text-body-md control-hoverable focus:ring-1 focus:border-primary transition" value="{{ old('institution') }}" />
              </div>

              <div class="col-span-1">
                <label class="block text-label-caps text-on-surface-variant mb-2">Tempat Penelitian</label>
                <input id="placeInput" name="research_place" class="w-full px-4 py-2 bg-slate-50 border border-outline-variant rounded-lg text-body-md control-hoverable focus:ring-1 focus:border-primary transition" value="{{ old('research_place') }}" />
              </div>

              <div class="col-span-2">
                <label class="block text-label-caps text-on-surface-variant mb-2">Pilih Admin yang Mengurus</label>
                <select id="adminSelect" name="admin_id" class="w-full px-4 py-2 bg-slate-50 border border-outline-variant rounded-lg control-hoverable focus:ring-1 focus:border-primary transition">
                  <option value="">-- Pilih admin --</option>
                  @foreach($admins as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="p-lg border-t border-slate-100 bg-slate-50 flex items-center justify-between">
              <div class="flex gap-2">
                <button type="button" id="generateBtn" class="px-4 py-2 bg-white border border-outline-variant rounded-lg btn-hoverable">Generate Preview</button>
                <button type="button" id="resetBtn" class="px-4 py-2 bg-white border border-outline-variant rounded-lg btn-hoverable">Reset</button>
              </div>
              <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">Generate & Simpan Draft</button>
              </div>
            </div>
          </div>
        </form>

        <div class="bg-white border border-outline-variant rounded-xl shadow-sm">
          <div class="px-lg py-md bg-slate-50 border-b border-outline-variant flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">visibility</span>
              <h3 class="font-h3 text-[16px] text-blue-900">Draft Preview</h3>
            </div>
            <div></div>
          </div>

          <div class="p-xl flex justify-center bg-slate-200/50">
            <div id="paper" class="w-[595px] bg-white shadow-2xl p-xl border border-slate-200 relative min-h-[842px] font-serif text-[12px] leading-relaxed text-[#1a1a1a]">
              <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                <span class="material-symbols-outlined text-[400px]">verified</span>
              </div>

              <div class="text-center mb-xl border-b-2 border-double border-black pb-4">
                <h4 class="font-bold text-[16px] uppercase leading-tight">KOMITE ETIK PENELITIAN KESEHATAN</h4>
                <h4 class="font-bold text-[18px] uppercase mb-1">UNIVERSITAS DIGITAL INDONESIA</h4>
                <p class="text-[10px] leading-tight italic">Jl. Kampus Merdeka No. 123, Jakarta Selatan, 12345. Telp: (021) 555-0123</p>
                <p class="text-[10px] leading-tight font-sans">Email: ethics-committee@udi.ac.id | Web: ethics.udi.ac.id</p>
              </div>

              <div class="text-center mb-xl">
                <h5 class="font-bold text-[14px] underline uppercase">KETERANGAN KELAIKAN ETIK</h5>
                <p class="font-sans font-medium text-[11px] mt-1">(ETHICAL CLEARANCE)</p>
                <p class="text-[12px] mt-2" id="previewNumber">Nomor: -</p>
              </div>

              <div class="space-y-4 px-8 text-justify">
                <p>Komite Etik Penelitian Kesehatan Universitas Digital Indonesia setelah mempelajari protokol penelitian yang diajukan, dengan ini menyatakan bahwa penelitian dengan judul:</p>
                <p class="font-bold text-center py-2 px-4 italic" id="previewTitle">"-"</p>
                <div class="grid grid-cols-12 gap-y-2 mt-4">
                  <div class="col-span-4 font-bold">Peneliti Utama</div>
                  <div class="col-span-8" id="previewPI">: -</div>

                  <div class="col-span-4 font-bold">Anggota Peneliti</div>
                  <div class="col-span-8" id="previewMembers">: -</div>

                  <div class="col-span-4 font-bold">Institusi</div>
                  <div class="col-span-8" id="previewInstitution">: -</div>

                  <div class="col-span-4 font-bold">Tempat Penelitian</div>
                  <div class="col-span-8" id="previewPlace">: -</div>
                </div>

                <p class="mt-6">Dinyatakan <strong>LAIK ETIK</strong> untuk dilaksanakan. Sertifikat ini berlaku selama 1 (satu) tahun terhitung sejak tanggal diterbitkan.</p>
              </div>

              <div class="mt-xl grid grid-cols-2">
                <div class="col-start-2 text-center">
                  <p id="previewDate">Jakarta, ____________</p>
                  <p class="mb-16">Ketua Komite Etik,</p>
                  <div class="relative inline-block">
                    <p class="font-bold underline">Prof. Dr. Ir. Budi Santoso, M.Eng</p>
                    <p>NIP. 197503122001121002</p>
                  </div>
                </div>
              </div>

              <div class="absolute bottom-10 left-10 right-10 flex justify-between items-end border-t border-slate-100 pt-2 opacity-40">
                <div class="flex items-center gap-2">
                  <img alt="QR Code" class="w-12 h-12 rounded-none" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='48' height='48'><rect width='48' height='48' fill='%23ffffff' stroke='%23e5e7eb'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='10' fill='%23666'>QR</text></svg>"/>
                  <p class="text-[8px] leading-tight">Scan untuk verifikasi keaslian<br/>dokumen secara online.</p>
                </div>
                <p class="text-[8px]" id="previewMeta">Halaman 1 dari 1 | Cetakan Sistem: -</p>
              </div>
            </div>
          </div>
          <div class="p-lg bg-slate-50 border-t border-outline-variant flex items-center justify-end">
            <div class="flex gap-3">
              <form id="sendForm" method="POST" action="" class="inline">
                @csrf
                <button type="button" id="sendAdminBtn" class="flex items-center gap-2 px-6 py-3 bg-white border text-on-surface font-button rounded-lg btn-hoverable">Send to Admin</button>
                <button type="button" id="sendBtn" class="flex items-center gap-2 px-6 py-3 bg-primary text-white font-button rounded-lg btn-hoverable">Send to Submitter</button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
    (function(){
    const generateBtn = document.getElementById('generateBtn');
    const resetBtn = document.getElementById('resetBtn');
    const proposalIdInput = document.getElementById('proposalIdInput');

    const titleInput = document.getElementById('titleInput');
    const piInput = document.getElementById('piInput');
    const membersInput = document.getElementById('membersInput');
    const institutionInput = document.getElementById('institutionInput');
    const placeInput = document.getElementById('placeInput');
    const adminSelect = document.getElementById('adminSelect');

    const previewTitle = document.getElementById('previewTitle');
    const previewPI = document.getElementById('previewPI');
    const previewMembers = document.getElementById('previewMembers');
    const previewInstitution = document.getElementById('previewInstitution');
    const previewPlace = document.getElementById('previewPlace');
    const previewMeta = document.getElementById('previewMeta');
    const previewNumber = document.getElementById('previewNumber');
    const previewDate = document.getElementById('previewDate');

    generateBtn && generateBtn.addEventListener('click', function(){
      previewTitle.textContent = '"' + (titleInput.value || '-') + '"';
      previewPI.textContent = ': ' + (piInput.value || '-');
      previewMembers.innerHTML = ': ' + (membersInput.value ? membersInput.value.replace(/\n/g, '<br/>') : '-');
      previewInstitution.textContent = ': ' + (institutionInput.value || '-');
      previewPlace.textContent = ': ' + (placeInput.value || '-');
      previewMeta.textContent = 'Halaman 1 dari 1 | Cetakan Sistem: ' + new Date().toLocaleString();

      // Keep number and date blank per requirement
      previewNumber.textContent = 'Nomor: -';
      previewDate.textContent = 'Jakarta, ____________';

      // prepare send form action if a draft doc exists in the drafts list (not implemented here)
    });

    resetBtn && resetBtn.addEventListener('click', function(){
      titleInput.value = '';
      piInput.value = '';
      membersInput.value = '';
      institutionInput.value = '';
      placeInput.value = '';

      previewTitle.textContent = '"-"';
      previewPI.textContent = ': -';
      previewMembers.textContent = ': -';
      previewInstitution.textContent = ': -';
      previewPlace.textContent = ': -';
      previewMeta.textContent = 'Halaman 1 dari 1 | Cetakan Sistem: -';
      previewNumber.textContent = 'Nomor: -';
      previewDate.textContent = 'Jakarta, ____________';
    });

    // Clicking a proposal in the left queue will populate the inputs and mark selection
    document.querySelectorAll('[data-proposal]').forEach(function(el){
      el.addEventListener('click', function(){
        const p = JSON.parse(this.dataset.proposal || '{}');
        titleInput.value = p.title || '';
        // Prefer nama_peneliti from proposal table; fall back to researcher account name
        piInput.value = p.nama_peneliti || p.researcher?.name || '';
        membersInput.value = (p.research_team || '') || '';
        institutionInput.value = p.institution || p.nama_institusi || '';
        placeInput.value = p.research_place || '';
        // set hidden proposal id so server receives the association
        if (proposalIdInput) proposalIdInput.value = p.id || '';
        // visual selection: remove from others, add to this
        document.querySelectorAll('.proposal-item').forEach(function(x){ x.classList.remove('proposal-selected'); });
        this.classList.add('proposal-selected');
        // scroll to form area for editing
        window.scrollTo({ top: 180, behavior: 'smooth' });
      });
    });

    // Send buttons: implement confirm + POST for Send to Admin
    const sendBtn = document.getElementById('sendBtn');
    const sendAdminBtn = document.getElementById('sendAdminBtn');
    const sendForm = document.getElementById('sendForm');
    const sendToAdminUrl = "{{ route('sekretaris.draf-ethical-clearance.sendToAdmin') }}";

    sendAdminBtn && sendAdminBtn.addEventListener('click', function(){
      if (!proposalIdInput || !proposalIdInput.value) return alert('Pilih proposal terlebih dahulu di antrean.');
      if (!adminSelect || !adminSelect.value) return alert('Pilih admin yang akan menerima draft.');

      if (!confirm('Yakin ingin mengirim draft ini ke admin?')) return;

      const token = sendForm.querySelector('input[name="_token"]')?.value || '';
      fetch(sendToAdminUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ proposal_id: proposalIdInput.value, admin_id: adminSelect.value }),
      }).then(async function(response) {
        const text = await response.text();
        let data = null;
        try {
          data = text ? JSON.parse(text) : null;
        } catch (parseError) {
          console.error('Response parse error', parseError, text);
          throw new Error('Server response tidak valid.');
        }

        if (!response.ok) {
          const message = data?.message || data?.error || 'Gagal mengirim draft. (' + response.status + ')';
          throw new Error(message);
        }

        if (data && data.status === 'ok') {
          alert(data.message || 'Draft berhasil dikirim ke admin.');
          window.location.reload();
        } else {
          throw new Error(data?.message || 'Gagal mengirim draft.');
        }
      }).catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat mengirim draft: ' + (err.message || 'Unknown error'));
      });
    });

    sendBtn && sendBtn.addEventListener('click', function(){
      alert('Stub: Mengirim draft ke submitter (belum diimplementasikan backend).');
    });

  })();
</script>

@endsection
