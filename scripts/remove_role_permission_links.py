from pathlib import Path
p = Path(r'd:\laragon\www\E-Protocol-Ethics-System\resources\views\layouts\admin.blade.php')
text = p.read_text(encoding='utf-8')
old = '''        <a href="{{ route('admin.role&permission.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.role&permission.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-user-shield text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.role&permission.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Role & Permission
        </a>\n\n'''
count = text.count(old)
if count == 0:
    raise SystemExit('Role & Permission block not found')
text = text.replace(old, '', 2)
p.write_text(text, encoding='utf-8')
print(f'Removed {count} occurrences')
