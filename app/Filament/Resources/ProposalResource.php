<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Filament\Resources\ProposalResource\RelationManagers;
use App\Models\Proposal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Administration';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-proposals');
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('create-proposals');
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('edit-proposals');
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('delete-proposals');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-proposals');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Content Area (Left Side)
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Proposal Information')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Proposal Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('regenerate')
                                            ->icon('heroicon-m-arrow-path')
                                            ->tooltip('Regenerate slug from title')
                                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                                $title = $get('title');
                                                if ($title) {
                                                    $set('slug', Str::slug($title));
                                                }
                                            })
                                    )
                                    ->columnSpanFull(),

                                  Forms\Components\TextInput::make('proposal_number')
                                    ->label('Invoice Number')
                                    ->required()
                                    ->placeholder('Contoh: QUO / 001 / XII / 26 / NEW')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                  Forms\Components\Placeholder::make('')
                                    ->content(new HtmlString('
                                       <div class="text-sm">
                                          <p class="font-semibold mb-2">Format: QUO / Nomor / Bulan (Romawi) / Tahun / Kode</p>
                                          <p> Kode:</p>
                                             <ul class="list-disc list-inside space-y-1 ml-2">
                                                <li>NEW - down payment</li>
                                                <li>REV - revisi</li>
                                                <li>REN - perpanjangan</li>
                                                <li>ADD - add-ons / tambahan</li>
                                             </ul>
                                       </div>
                                    '))
                                    ->columnSpanFull(),

                                  Forms\Components\TextInput::make('client_name')
                                    ->label('Client Name')
                                    ->required()
                                    ->maxLength(255),

                                  Forms\Components\TextInput::make('company_name')
                                    ->label('Company')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Package Details')
                            ->schema([
                                Forms\Components\Repeater::make('packages')
                                    ->label('Packages')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Package Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('price')
                                            ->label('Price')
                                            ->required()
                                            ->maxLength(255)
                                            ->prefix('Rp'),

                                        Forms\Components\TextInput::make('renewal_price')
                                            ->label('Renewal Price')
                                            ->maxLength(255)
                                            ->prefix('Rp'),

                                        Forms\Components\TextInput::make('price_coret')
                                            ->label('Price Coret')
                                            ->maxLength(255)
                                            ->prefix('Rp'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add Package')
                                    ->addActionAlignment('start')
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_berlaku_tahun')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('berlaku_x_tahun')
                                    ->label('Berlaku untuk X tahun')
                                    ->helperText('Contoh: Biaya tahun pertama dan kedua')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->collapsible(),

                        Forms\Components\Section::make('Project Brief')
                            ->schema([
                                // Brand Project
                                Forms\Components\Radio::make('brand_project')
                                    ->label('Brand Logo')
                                    ->options([
                                        'logobrand-1' => 'Logo Brand 1',
                                        'logobrand-2' => 'Logo Brand 2',
                                    ])
                                    ->descriptions([
                                        'logobrand-1' => new HtmlString('<img src="' . asset('images/logobrand-1.png') . '" alt="Logo Brand 1" style="max-width: 200px; max-height: 100px; margin-top: 8px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px;">'),
                                        'logobrand-2' => new HtmlString('<img src="' . asset('images/logobrand-2.png') . '" alt="Logo Brand 2" style="max-width: 200px; max-height: 100px; margin-top: 8px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px;">'),
                                    ])
                                    ->default('logobrand-1')
                                    ->required()
                                    ->columnSpanFull(),
                                 
                                // Separator
                                Forms\Components\Placeholder::make('separator_2')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Short Brief Section
                                Forms\Components\CheckboxList::make('short_brief')
                                    ->label('Short Brief')
                                    ->options([
                                        'Desain modern yang menonjolkan kredibilitas dan profesionalisme bisnis / perusahaan' => 'Desain modern yang menonjolkan kredibilitas dan profesionalisme bisnis / perusahaan',
                                        'Tampilan responsive menyesuaikan device (desktop, handphone, dan tablet) sehingga layout website selalu tampil secara optimal' => 'Tampilan responsive menyesuaikan device (desktop, handphone, dan tablet) sehingga layout website selalu tampil secara optimal',
                                        'Website cepat dan aman untuk diakses' => 'Website cepat dan aman untuk diakses',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('short_brief_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Brief Item')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_1')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Core Services
                                Forms\Components\CheckboxList::make('core_services')
                                    ->label('Core Services')
                                    ->options([
                                        'Custom desain profesional sampai dengan 5 halaman utama' => 'Custom desain profesional sampai dengan 5 halaman utama',
                                        'Custom desain profesional sampai dengan 7 halaman utama' => 'Custom desain profesional sampai dengan 7 halaman utama',
                                        'Custom desain profesional sampai dengan 8 halaman utama' => 'Custom desain profesional sampai dengan 8 halaman utama',
                                        'Custom desain profesional sampai dengan 10 halaman utama' => 'Custom desain profesional sampai dengan 10 halaman utama',
                                        'Custom desain profesional sampai dengan 15 halaman utama' => 'Custom desain profesional sampai dengan 15 halaman utama',
                                        'Custom desain profesional sampai dengan 20 halaman utama' => 'Custom desain profesional sampai dengan 20 halaman utama',
                                        'Desain responsive menyesuaikan 3 ukuran device (desktop, handphone, dan tablet)' => 'Desain responsive menyesuaikan 3 ukuran device (desktop, handphone, dan tablet)',
                                        'Domain dengan ekstensi .com (optional)' => 'Domain dengan ekstensi .com (optional)',
                                        'High performance cloud hosting' => 'High performance cloud hosting',
                                        'SSL certificate' => 'SSL certificate',
                                        'Support dan maintenance' => 'Support dan maintenance',
                                        'Pengembangan copywriting' => 'Pengembangan copywriting',
                                        'Stock aset premium berlisensi' => 'Stock aset premium berlisensi',
                                        'Admin dashboard: akses untuk mengelola website dan mengupdate konten dengan mudah' => 'Admin dashboard: akses untuk mengelola website dan mengupdate konten dengan mudah',
                                        'User guide: panduan untuk mengupdate konten secara mandiri' => 'User guide: panduan untuk mengupdate konten secara mandiri',
                                        'White label: website Anda adalah brand Anda, Imajiner tidak meninggalkan signature pada website yang dibuat' => 'White label: website Anda adalah brand Anda, Imajiner tidak meninggalkan signature pada website yang dibuat',
                                        'SEO friendly website' => 'SEO friendly website',
                                        'Integrasi Google Analytics dan Google Search Console: untuk mendapatkan data statistik pengunjung dan performansi SEO' => 'Integrasi Google Analytics dan Google Search Console: untuk mendapatkan data statistik pengunjung dan performansi SEO',
                                        'Integrasi Google Tag Manager dan Meta Script' => 'Integrasi Google Tag Manager dan Meta Script',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('core_services_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Service')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_2')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Standard Features Section
                                Forms\Components\CheckboxList::make('standard_features')
                                    ->label('Standard Features')
                                    ->options([
                                        'Dapat menggunakan semua fitur standar yang tersedia (contoh dapat dilihat di sini)' => 'Dapat menggunakan semua fitur standar yang tersedia (contoh dapat dilihat di sini)',
                                        'Unlimited halaman standard (dapat dibuat secara mandiri)' => 'Unlimited halaman standard (dapat dibuat secara mandiri)',
                                        'Dual Language Feature (autotranslation by Gtranslate)' => 'Dual Language Feature (autotranslation by Gtranslate)',
                                        'Dual Language Feature (manual translation)' => 'Dual Language Feature (manual translation)',
                                        'WhatsApp Button Chat' => 'WhatsApp Button Chat',
                                        'Banner gambar atau video' => 'Banner gambar atau video',
                                        'Gallery untuk menampilkan foto dan informasi perusahaan' => 'Gallery untuk menampilkan foto dan informasi perusahaan',
                                        'Google Map pada halaman kontak' => 'Google Map pada halaman kontak',
                                        'Contact Form' => 'Contact Form',
                                        'Koneksi media sosial (tautan ke halaman media sosial)' => 'Koneksi media sosial (tautan ke halaman media sosial)',
                                        'Archive Posts: halaman direktori artikel (blog) untuk mencantumkan berbagai postingan artikel berikut dengan kategori, sub-kategori, tags' => 'Archive Posts: halaman direktori artikel (blog) untuk mencantumkan berbagai postingan artikel berikut dengan kategori, sub-kategori, tags',
                                        'Single Post: halaman individu untuk masing-masing postingan artikel yang berisi detil konten lebih lanjut' => 'Single Post: halaman individu untuk masing-masing postingan artikel yang berisi detil konten lebih lanjut',
                                        'Archive Products: halaman direktori / katalog untuk mencantumkan listing produk' => 'Archive Products: halaman direktori / katalog untuk mencantumkan listing produk',
                                        'Single Product: halaman individu untuk masing-masing produk untuk menampilkan informasi lebih lanjut (tidak ada sistem e-commerce)' => 'Single Product: halaman individu untuk masing-masing produk untuk menampilkan informasi lebih lanjut (tidak ada sistem e-commerce)',
                                        'On-page SEO: pengaturan / optimasi custom meta title / description untuk setiap halaman' => 'On-page SEO: pengaturan / optimasi custom meta title / description untuk setiap halaman',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('standard_features_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Feature')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_3')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Asset Section
                                Forms\Components\CheckboxList::make('asset')
                                    ->label('Asset')
                                    ->options([
                                        'Premium stock aset dari freepik.com' => 'Premium stock aset dari freepik.com',
                                        'Premium stock aset dari elements.envato.com' => 'Premium stock aset dari elements.envato.com',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('asset_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Asset')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_4')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Server Section
                                Forms\Components\CheckboxList::make('server')
                                    ->label('Server')
                                    ->options([
                                        'High Performance Cloud Server' => 'High Performance Cloud Server',
                                        'Kecepatan tinggi dengan tingkat uptime 99,9%' => 'Kecepatan tinggi dengan tingkat uptime 99,9%',
                                        'Average speed load kurang dari 4 second' => 'Average speed load kurang dari 4 second',
                                        'Kapasitas hingga 2000 pengunjung unik setiap harinya' => 'Kapasitas hingga 2000 pengunjung unik setiap harinya',
                                        'Disk Space SSD 8GB' => 'Disk Space SSD 8GB',
                                        'Disk Space SSD 20GB' => 'Disk Space SSD 20GB',
                                        'Unlimited Bandwidth' => 'Unlimited Bandwidth',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('server_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Server')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_5')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Security Section
                                Forms\Components\CheckboxList::make('security')
                                    ->label('Security')
                                    ->options([
                                        'SSL (Secure Socket Layer) dengan rating A untuk keamanan pengunjung dan meningkatkan reputasi web di Search Engine' => 'SSL (Secure Socket Layer) dengan rating A untuk keamanan pengunjung dan meningkatkan reputasi web di Search Engine',
                                        'Anti-Malware sebagai proteksi terhadap virus dan hacker' => 'Anti-Malware sebagai proteksi terhadap virus dan hacker',
                                        'Perlindungan dari Brute Force dan DDoS' => 'Perlindungan dari Brute Force dan DDoS',
                                        'Auto-banned IP dengan history yang diduga berbahaya' => 'Auto-banned IP dengan history yang diduga berbahaya',
                                        'Daily malware scanning' => 'Daily malware scanning',
                                        'Blacklist hostname asing yang berusaha login dengan kredensial admin' => 'Blacklist hostname asing yang berusaha login dengan kredensial admin',
                                        'Backup data secara harian di server atau Google Drive' => 'Backup data secara harian di server atau Google Drive',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('security_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Security')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_6')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Support Section
                                Forms\Components\CheckboxList::make('support')
                                    ->label('Support')
                                    ->options([
                                        'Upload konten hingga 20 gambar / produk / post ke website untuk mengisi konten awal' => 'Upload konten hingga 20 gambar / produk / post ke website untuk mengisi konten awal',
                                        'Major Revision: penambahan fitur dan revisi yang bersifat mayor (tata letak dan desain) selama timeline pengerjaan, dengan batas hingga 4 kali revisi.' => 'Major Revision: penambahan fitur dan revisi yang bersifat mayor (tata letak dan desain) selama timeline pengerjaan, dengan batas hingga 4 kali revisi.',
                                        'Major Revision: penambahan fitur dan revisi yang bersifat mayor (tata letak dan desain) hingga 30 hari setelah website selesai, dengan batas hingga 8 kali revisi.' => 'Major Revision: penambahan fitur dan revisi yang bersifat mayor (tata letak dan desain) hingga 30 hari setelah website selesai, dengan batas hingga 8 kali revisi.',
                                        'Minor Revision: perubahan pada website yang bersifat minor (perubahan teks, gambar, warna) hingga 7 hari setelah website selesai' => 'Minor Revision: perubahan pada website yang bersifat minor (perubahan teks, gambar, warna) hingga 7 hari setelah website selesai',
                                        'Minor Revision: perubahan pada website yang bersifat minor (perubahan teks, gambar, warna) hingga 30 hari setelah website selesai' => 'Minor Revision: perubahan pada website yang bersifat minor (perubahan teks, gambar, warna) hingga 30 hari setelah website selesai',
                                        'Server support: support dari sisi server (server uptime, performansi, konfigurasi SSL, security server) selama 12 bulan' => 'Server support: support dari sisi server (server uptime, performansi, konfigurasi SSL, security server) selama 12 bulan',
                                        'Server support: support dari sisi server (server uptime, performansi, konfigurasi SSL, security server) selama 24 bulan' => 'Server support: support dari sisi server (server uptime, performansi, konfigurasi SSL, security server) selama 24 bulan',
                                        'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 6 bulan' => 'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 6 bulan',
                                        'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 12 bulan' => 'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 12 bulan',
                                        'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 24 bulan' => 'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 24 bulan',
                                        'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 6 bulan' => 'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 6 bulan',
                                        'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 12 bulan' => 'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 12 bulan',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 3 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 3 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 5 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 5 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 10 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 10 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 15 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 15 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 20 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 20 kali (request) / bulan, tidak terakumulasi',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 2 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 2 jam',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 4 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 4 jam',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 6 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 6 jam',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 8 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 8 jam',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('support_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Support')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_7')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Text Portfolio
                                Forms\Components\RichEditor::make('text_portfolio')
                                    ->label('Portfolio')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'heading',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'codeBlock',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),

                                // Gallery Portfolio
                                Forms\Components\FileUpload::make('gallery_portfolio')
                                    ->label('Gallery Portfolio')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(10)
                                    ->directory('portfolios/gallery')
                                    ->imageEditor()
                                    ->columnSpanFull(),

                                // Additional Notes
                                Forms\Components\RichEditor::make('additional_notes')
                                    ->label('Additional Notes')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'heading',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'codeBlock',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_timeline')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Timeline Project
                                Forms\Components\Repeater::make('timeline_project')
                                    ->label('Timeline Project')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Judul')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('days')
                                            ->label('Jumlah Hari')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(0)
                                            ->suffix('hari')
                                            ->columnSpanFull(),
                                    ])
                                    ->default([
                                        ['title' => 'Pembayaran DP', 'days' => 1],
                                        ['title' => 'Inisiasi Project', 'days' => 1],
                                        ['title' => 'Pengumpulan Materi', 'days' => 2],
                                        ['title' => 'Pembuatan Kerangka (FIGMA Home)', 'days' => 5],
                                        ['title' => 'Review Kerangka', 'days' => 2],
                                        ['title' => 'Proses desain (FIGMA ALL)', 'days' => 5],
                                        ['title' => 'Review Desain', 'days' => 2],
                                        ['title' => 'Update Revisi Desain', 'days' => 3],
                                        ['title' => 'Review Revisi Desain', 'days' => 2],
                                        ['title' => 'Proses development', 'days' => 5],
                                        ['title' => 'Proses revisi', 'days' => 4],
                                        ['title' => 'Update Revisi Development', 'days' => 0],
                                        ['title' => 'Review Revisi Development', 'days' => 0],
                                        ['title' => 'Finishing & Pelunasan', 'days' => 2],
                                        ['title' => 'Pemberian Akses + Guide', 'days' => 1],
                                        ['title' => 'Desain & development (tanpa FIGMA)', 'days' => 0],
                                    ])
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->addActionLabel('Add Timeline Item')
                                    ->addActionAlignment('start')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Client Benefit')
                            ->schema([
                                Forms\Components\RichEditor::make('add_ons_features')
                                    ->label('Add-ons Features')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'heading',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'codeBlock',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Other Projects')
                            ->schema([
                                Forms\Components\CheckboxList::make('ecommerce_features')
                                    ->label('Ecommerce Features')
                                    ->options([
                                        'Platform: WooCommerce (Plugin e-commerce WordPress)' => 'Platform: WooCommerce (Plugin e-commerce WordPress)',
                                        'Katalog Produk: Halaman katalog produk yang terstruktur dengan baik' => 'Katalog Produk: Halaman katalog produk yang terstruktur dengan baik',
                                        'Kategori, sub-kategori, merek (brand), dan tag untuk pengelompokan produk' => 'Kategori, sub-kategori, merek (brand), dan tag untuk pengelompokan produk',
                                        'Halaman Produk Individu (Single Product): Deskripsi produk yang detail' => 'Halaman Produk Individu (Single Product): Deskripsi produk yang detail',
                                        'Galeri gambar produk' => 'Galeri gambar produk',
                                        'Informasi harga (termasuk harga diskon/sale, jika ada)' => 'Informasi harga (termasuk harga diskon/sale, jika ada)',
                                        'Atribut produk' => 'Atribut produk',
                                        'Tombol "Order via WhatsApp" (call-to-action langsung)' => 'Tombol "Order via WhatsApp" (call-to-action langsung)',
                                        'Fitur Pencarian (Search Bar): Memudahkan pengunjung menemukan produk yang dicari' => 'Fitur Pencarian (Search Bar): Memudahkan pengunjung menemukan produk yang dicari',
                                        'Fitur Wishlist: Pengguna dapat menyimpan produk yang diminati untuk dibeli' => 'Fitur Wishlist: Pengguna dapat menyimpan produk yang diminati untuk dibeli',
                                        'Single Currency: satu jenis mata uang – contoh: IDR (Rupiah)' => 'Single Currency: satu jenis mata uang – contoh: IDR (Rupiah)',
                                        'Harga diskon/sale reguler' => 'Harga diskon/sale reguler',
                                        'Diskon menggunakan kode kupon, dapat berupa potongan nominal atau persen' => 'Diskon menggunakan kode kupon, dapat berupa potongan nominal atau persen',
                                        'Metode pembayaran via Transfer Bank (manual)' => 'Metode pembayaran via Transfer Bank (manual)',
                                        'Verifikasi pembayaran secara manual' => 'Verifikasi pembayaran secara manual',
                                        'Integrasi Pembayaran: Payment Gateway Midtrans (integrasi menggunakan plugin resmi dari Midtrans)' => 'Integrasi Pembayaran: Payment Gateway Midtrans (integrasi menggunakan plugin resmi dari Midtrans)',
                                        'Perhitungan Ongkos Kirim Ekspedisi Eksternal: Integrasi dengan RajaOngkir API Pro (menggunakan plugin Woongkir)' => 'Perhitungan Ongkos Kirim Ekspedisi Eksternal: Integrasi dengan RajaOngkir API Pro (menggunakan plugin Woongkir)',
                                        'Perhitungan Ongkos Kirim Ekspedisi Internal: Kalkulasi berdasarkan daerah pengiriman, berat, dan volume' => 'Perhitungan Ongkos Kirim Ekspedisi Internal: Kalkulasi berdasarkan daerah pengiriman, berat, dan volume',
                                        'Email notifikasi otomatis ke penjual dan pembeli yang berisi status pesanan, pembayaran, dll' => 'Email notifikasi otomatis ke penjual dan pembeli yang berisi status pesanan, pembayaran, dll',
                                        'Fitur cek status order: Pengguna dapat melacak status order dengan memasukkan nomor order' => 'Fitur cek status order: Pengguna dapat melacak status order dengan memasukkan nomor order',
                                        'Blog/Artikel/Berita: Untuk publikasi konten yang relevan dan meningkatkan SEO' => 'Blog/Artikel/Berita: Untuk publikasi konten yang relevan dan meningkatkan SEO',
                                        'Galeri: Menampilkan foto dan informasi perusahaan' => 'Galeri: Menampilkan foto dan informasi perusahaan',
                                        'Banner: Informasi visual yang menarik perhatian (gambar atau video)' => 'Banner: Informasi visual yang menarik perhatian (gambar atau video)',
                                        'Form kontak ("Contact Form" / "Get a Quote")' => 'Form kontak ("Contact Form" / "Get a Quote")',
                                        'Integrasi Google Maps (menampilkan lokasi bisnis)' => 'Integrasi Google Maps (menampilkan lokasi bisnis)',
                                        'Koneksi Media Sosial (tautan ke halaman media sosial)' => 'Koneksi Media Sosial (tautan ke halaman media sosial)',
                                        'On-page SEO: pengaturan / optimasi custom meta title / description untuk setiap halaman' => 'On-page SEO: pengaturan / optimasi custom meta title / description untuk setiap halaman',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('ecommerce_features_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Ecommerce Feature')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_pro_features')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Pro Features
                                Forms\Components\CheckboxList::make('pro_features')
                                    ->label('Pro Features')
                                    ->options([
                                        'Premium & interactive custom design' => 'Premium & interactive custom design',
                                        'Diberikan source code' => 'Diberikan source code',
                                        'Domain dengan ekstensi .com / .co.id / .id (opsional)' => 'Domain dengan ekstensi .com / .co.id / .id (opsional)',
                                        'Custom desain profesional sampai dengan 10 halaman utama' => 'Custom desain profesional sampai dengan 10 halaman utama',
                                        'Custom desain profesional sampai dengan 15 halaman utama' => 'Custom desain profesional sampai dengan 15 halaman utama',
                                        'Custom desain profesional sampai dengan 20 halaman utama' => 'Custom desain profesional sampai dengan 20 halaman utama',
                                        'Dual Language Feature with Advanced Translation (input manual)' => 'Dual Language Feature with Advanced Translation (input manual)',
                                        'Kontak via Live Chat, memudahkan pengunjung web untuk menghubungi lewat Live Chat (optional)' => 'Kontak via Live Chat, memudahkan pengunjung web untuk menghubungi lewat Live Chat (optional)',
                                        'Server dengan Disk Space SSD 20GB' => 'Server dengan Disk Space SSD 20GB',
                                        'Instagram Auto Feed' => 'Instagram Auto Feed',
                                        'Multi-admin WhatsApp Button' => 'Multi-admin WhatsApp Button',
                                        'Integrasi MailChimp untuk subscribe newsletter' => 'Integrasi MailChimp untuk subscribe newsletter',
                                        'Fitur Archive Posts: halaman arsip artikel (blog) untuk mencantumkan berbagai postingan artikel berikut dengan kategori, sub-kategori, tags' => 'Fitur Archive Posts: halaman arsip artikel (blog) untuk mencantumkan berbagai postingan artikel berikut dengan kategori, sub-kategori, tags',
                                        'Fitur Single Post: halaman individu untuk masing-masing postingan artikel yang berisi detil konten lebih lanjut' => 'Fitur Single Post: halaman individu untuk masing-masing postingan artikel yang berisi detil konten lebih lanjut',
                                        'Fitur Archive Products Catalogue: halaman katalog untuk mencantumkan listing produk' => 'Fitur Archive Products Catalogue: halaman katalog untuk mencantumkan listing produk',
                                        'Fitur Single Product: halaman individu untuk masing-masing produk untuk menampilkan informasi lebih lanjut (tidak ada sistem e-commerce)' => 'Fitur Single Product: halaman individu untuk masing-masing produk untuk menampilkan informasi lebih lanjut (tidak ada sistem e-commerce)',
                                        'Professional Translation Service (ENG / ID) up to 5000 words' => 'Professional Translation Service (ENG / ID) up to 5000 words',
                                        'Professional Copywriting Service (ENG / ID) up to 5000 words' => 'Professional Copywriting Service (ENG / ID) up to 5000 words',
                                        'Premium Stock Assets (photo, icon, video) up to 30 assets' => 'Premium Stock Assets (photo, icon, video) up to 30 assets',
                                        'High Performance Cloud Server: Kecepatan tinggi dengan tingkat uptime 99,9%, Average speed load kurang dari 4 second, Kapasitas hingga 2000 pengunjung unik setiap harinya, Disk Space SSD 8GB, Unlimited Bandwidth' => 'High Performance Cloud Server: Kecepatan tinggi dengan tingkat uptime 99,9%, Average speed load kurang dari 4 second, Kapasitas hingga 2000 pengunjung unik setiap harinya, Disk Space SSD 8GB, Unlimited Bandwidth',
                                        'High Performance Cloud Server: Kecepatan tinggi dengan tingkat uptime 99,9%, Average speed load kurang dari 4 second, Kapasitas hingga 2000 pengunjung unik setiap harinya, Disk Space SSD 20GB, Unlimited Bandwidth' => 'High Performance Cloud Server: Kecepatan tinggi dengan tingkat uptime 99,9%, Average speed load kurang dari 4 second, Kapasitas hingga 2000 pengunjung unik setiap harinya, Disk Space SSD 20GB, Unlimited Bandwidth',
                                        'High Performance Cloud Server: 1vCPU, 2GB RAM, Kecepatan tinggi dengan tingkat uptime 99,9%, Average speed load kurang dari 4 second, Kapasitas hingga 2000 pengunjung unik setiap harinya, Disk Space SSD 50GB, Unlimited Bandwidth' => 'High Performance Cloud Server: 1vCPU, 2GB RAM, Kecepatan tinggi dengan tingkat uptime 99,9%, Average speed load kurang dari 4 second, Kapasitas hingga 2000 pengunjung unik setiap harinya, Disk Space SSD 50GB, Unlimited Bandwidth',
                                        'SSL (Secure Socket Layer) dengan rating A untuk keamanan pengunjung dan meningkatkan reputasi web di Search Engine' => 'SSL (Secure Socket Layer) dengan rating A untuk keamanan pengunjung dan meningkatkan reputasi web di Search Engine',
                                        'Backup data secara harian di server atau Google Drive' => 'Backup data secara harian di server atau Google Drive',
                                        'Major Revision: penambahan fitur dan revisi yang bersifat mayor (tata letak dan desain) hingga 30 hari setelah website selesai, dengan batas hingga 8 kali revisi.' => 'Major Revision: penambahan fitur dan revisi yang bersifat mayor (tata letak dan desain) hingga 30 hari setelah website selesai, dengan batas hingga 8 kali revisi.',
                                        'Minor Revision: perubahan pada website yang bersifat minor (penggantian font, warna, foto, dan tulisan) 1x sebulan selama Help Desk support berjalan' => 'Minor Revision: perubahan pada website yang bersifat minor (penggantian font, warna, foto, dan tulisan) 1x sebulan selama Help Desk support berjalan',
                                        'Server support: support dari sisi server (server uptime, performansi, konfigurasi SSL, security server) selama 12 bulan' => 'Server support: support dari sisi server (server uptime, performansi, konfigurasi SSL, security server) selama 12 bulan',
                                        'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 12 bulan' => 'Application (web) support: support dari sisi web (performansi web, bug dan troubleshooting) 12 bulan',
                                        'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 6 bulan' => 'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 6 bulan',
                                        'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 12 bulan' => 'Help Desk support: support dari tim Imajiner yang dapat dihubungi melalui grup WhatsApp sewaktu menemukan kendala, selama 12 bulan',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 5 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 5 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 10 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 10 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 15 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 15 kali (request) / bulan, tidak terakumulasi',
                                        'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 20 kali (request) / bulan, tidak terakumulasi' => 'Update support: bantuan dari tim Imajiner untuk melakukan update konten pada website sebanyak 20 kali (request) / bulan, tidak terakumulasi',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 4 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 4 jam',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 6 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 6 jam',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 8 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 8 jam',
                                        'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 10 jam' => 'Technical support: support dari tim Imajiner untuk perubahan fitur, struktur web, dan penyesuaian teknis lainnya, sebanyak 10 jam',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('pro_features_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Pro Feature')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_ecommerce_pro_features')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                // Ecommerce Pro Features
                                Forms\Components\CheckboxList::make('ecommerce_pro_features')
                                    ->label('Ecommerce Pro Features')
                                    ->options([
                                        'Diberikan source code' => 'Diberikan source code',
                                        'Wishlist dan compare' => 'Wishlist dan compare',
                                        'Invoice dan packing slip' => 'Invoice dan packing slip',
                                        'Halaman khusus cek resi' => 'Halaman khusus cek resi',
                                        'Domain dengan ekstensi .com / .co.id / .id (opsional)' => 'Domain dengan ekstensi .com / .co.id / .id (opsional)',
                                        'Dual Language Feature with Advanced Translation (input manual)' => 'Dual Language Feature with Advanced Translation (input manual)',
                                        'Kontak via Live Chat, memudahkan pengunjung web untuk menghubungi lewat Live Chat (optional)' => 'Kontak via Live Chat, memudahkan pengunjung web untuk menghubungi lewat Live Chat (optional)',
                                        'Server dengan Disk Space SSD 20GB' => 'Server dengan Disk Space SSD 20GB',
                                        'Corporate Security + Content Delivery Network (CDN)' => 'Corporate Security + Content Delivery Network (CDN)',
                                        'VIP support 12 bulan, koresponden via WhatsApp Grup / Phone' => 'VIP support 12 bulan, koresponden via WhatsApp Grup / Phone',
                                        'Instagram Auto Feed' => 'Instagram Auto Feed',
                                        'Free 6 Instagram Post' => 'Free 6 Instagram Post',
                                        'Multi-admin WhatsApp Button' => 'Multi-admin WhatsApp Button',
                                        'Integrasi MailChimp untuk subscribe newsletter' => 'Integrasi MailChimp untuk subscribe newsletter',
                                        'Up to 3000 SKU' => 'Up to 3000 SKU',
                                        'Premium stock asset berlisensi' => 'Premium stock asset berlisensi',
                                        'Online marketing campaign up to 100 visitors' => 'Online marketing campaign up to 100 visitors',
                                    ])
                                    ->default([])
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('ecommerce_pro_features_custom')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->label('Custom Ecommerce Pro Feature')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add new choice')
                                    ->addActionAlignment('start')
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Sidebar (Right Side)
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Other Information')
                            ->schema([
                                Forms\Components\TextInput::make('project_manager')
                                    ->label('Project Manager')
                                    ->maxLength(255)
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('additional_image_qr')
                                    ->label('Additional Image (for QR)')
                                    ->helperText('Mendukung QR Code Figma/gambar. Info tambahan bisa diletakkan di caption.')
                                    ->image()
                                    ->directory('proposals/qr-images')
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->columnSpanFull(),

                                // Separator
                                Forms\Components\Placeholder::make('separator_garansi')
                                    ->label('')
                                    ->content(new HtmlString('<hr class="border-t border-gray-300 dark:border-gray-600 my-4">'))
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('aktifkan_garansi')
                                    ->label('Aktifkan Garansi')
                                    ->default(false)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Access Control')
                            ->schema([
                                Forms\Components\TextInput::make('lock_username')
                                    ->label('Username')
                                    ->default('digital')
                                    ->placeholder('Default: digital')
                                    ->required(fn (Forms\Get $get) => $get('is_locked'))
                                    ->disabled(fn (Forms\Get $get) => !$get('is_locked'))
                                    ->dehydrated(fn (Forms\Get $get) => $get('is_locked'))
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('lock_password')
                                    ->label('Password')
                                    ->default('proposal135')
                                    ->placeholder('Default: proposal135')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (Forms\Get $get) => $get('is_locked'))
                                    ->disabled(fn (Forms\Get $get) => !$get('is_locked'))
                                    ->dehydrated(fn (Forms\Get $get) => $get('is_locked'))
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('is_locked')
                                    ->label('Proteksi Password')
                                    ->default(true)
                                    ->live()
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Publishing')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'published' => 'Published',
                                        'draft' => 'Draft',
                                    ])
                                    ->default('published')
                                    ->required()
                                    ->native(false)
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->published_at !== null ? 'published' : 'draft');
                                        }
                                    })
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state === 'published') {
                                            // Set to now if publishing and no date set
                                            if (!$get('published_at')) {
                                                $set('published_at', now());
                                            }
                                        } else {
                                            // Clear date if setting to draft
                                            $set('published_at', null);
                                        }
                                    }),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publish Date')
                                    ->native(false)
                                    ->disabled(fn (Forms\Get $get) => $get('status') === 'draft')
                                    ->dehydrated(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('save')
                                        ->label(fn (string $operation) => $operation === 'create' ? 'Create' : 'Save Changes')
                                        ->submit('save')
                                        ->color('primary')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->button(),

                                    Forms\Components\Actions\Action::make('cancel')
                                        ->label('Cancel')
                                        ->url(fn () => ProposalResource::getUrl('index'))
                                        ->color('gray')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->button(),
                                ])
                                    ->fullWidth()
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn ($record) => ProposalResource::getUrl('edit', ['record' => $record])),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Nama Klien')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('proposal_number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Publish')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Draft'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
