<img src="{{ asset('images/logounib.png') }}" alt="Watermark UNIB" class="watermark" onerror="this.onerror=null; this.src='file://{{ base_path('public/images/logounib.png') }}';" />
<div class="container">
    <!-- Header + Title seperti gambar -->
    <div class="logo">
        <img src="{{ asset('images/logounib.png') }}" alt="Logo UNIB" onerror="this.onerror=null; this.src='file://{{ base_path('public/images/logounib.png') }}';" />
    </div>

    <div class="header">
        <h1>FAKULTAS TEKNIK</h1>
        <h2>UNIVERSITAS BENGKULU</h2>
    </div>

    <div class="title">
        <h3>SURAT KETERANGAN PENDAMPING IJAZAH</h3>
        <h4>DIPLOMA SUPPLEMENT</h4>
        <p class="number"><strong>Nomor : _______________</strong></p>
        <p>Surat Keterangan Pendamping Ijazah sebagai pelengkap Ijazah yang menerangkan capaian pembelajaran dan prestasi dari pemegang Ijazah selama studi</p>
        <p class="item-italic">The Diploma Supplement accompanies a higher education certificate providing a standardized description of the nature, level, content and status of studies completed by its holder</p>
    </div>

    <!-- I. Informasi Identitas Diri Pemegang SKPI -->
    <div class="section">
        <div class="section-title">
            I. INFORMASI TENTANG IDENTITAS DIRI PEMEGANG SKPI<br>
            <span>INFORMATION OF PERSONAL IDENTITY DIPLOMA SUPPLEMENT HOLDER</span>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.1 NAMA</span><br>
                    <span class="item-value">{{ strtoupper($skpi->nama_lengkap) }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Name</span><br>
                    <span class="item-value">{{ strtoupper($skpi->nama_lengkap) }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.2 Tempat dan Tanggal Lahir</span><br>
                    <span class="item-value">{{ $skpi->tempat_lahir }}, {{ \Carbon\Carbon::parse($skpi->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Place and Date of Birth</span><br>
                    <span class="item-value">{{ $skpi->tempat_lahir }}, {{ \Carbon\Carbon::parse($skpi->tanggal_lahir)->format('F j, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.3 Nomor Induk Mahasiswa (NPM)</span><br>
                    <span class="item-value">{{ $skpi->npm }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Student Identification Number</span><br>
                    <span class="item-value">{{ $skpi->npm }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.4 Tahun Masuk</span><br>
                    <span class="item-value">{{ \Carbon\Carbon::parse($skpi->tanggal_lulus)->subYears(4)->format('Y') }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Admission Year</span><br>
                    <span class="item-value">{{ \Carbon\Carbon::parse($skpi->tanggal_lulus)->subYears(4)->format('Y') }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.5 Tanggal Lulus</span><br>
                    <span class="item-value">Bengkulu, {{ \Carbon\Carbon::parse($skpi->tanggal_lulus)->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Date of Graduation</span><br>
                    <span class="item-value">Bengkulu, {{ \Carbon\Carbon::parse($skpi->tanggal_lulus)->format('F j, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.6 Nomor Ijazah/Nomor Seri</span><br>
                    <span class="item-value">{{ $skpi->nomor_ijazah }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Certificate Number</span><br>
                    <span class="item-value">{{ $skpi->nomor_ijazah }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">1.7 Gelar</span><br>
                    <span class="item-value">{{ $skpi->gelar }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Title</span><br>
                    <span class="item-value">{{ $skpi->gelar == 'S.T' ? 'Bachelor of Engineering (B.Eng)' : ($skpi->gelar == 'S.Kom' ? 'Bachelor of Computer Science (B.Cs)' : 'Bachelor Degree') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- II. Informasi Identitas Penyelenggara Program -->
    <div class="section">
        <div class="section-title">
            II. INFORMASI TENTANG IDENTITAS PENYELENGGARA PROGRAM<br>
            <span>INFORMATION OF IDENTITY HIGHER EDUCATION INSTITUTION</span>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.1 Surat Keterangan Pendirian</span><br>
                    <span class="item-value">Keputusan Presiden RI Nomor 17 Tahun 1982</span>
                </div>
                <div class="column">
                    <span class="item-italic">Certificate of Establishment</span><br>
                    <span class="item-value">Republic of Indonesia Presidential Instruction Number 17, 1982</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.2 Nama Perguruan Tinggi</span><br>
                    <span class="item-value">Universitas Bengkulu</span>
                </div>
                <div class="column">
                    <span class="item-italic">Name of University</span><br>
                    <span class="item-value">Bengkulu University</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.3 Fakultas</span><br>
                    <span class="item-value">Teknik</span>
                </div>
                <div class="column">
                    <span class="item-italic">Faculty</span><br>
                    <span class="item-value">Engineering</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.4 Nama Program Studi</span><br>
                    <span class="item-value">{{ $skpi->jurusan->nama_jurusan ?? 'Informatika' }}</span>
                </div>
                <div class="column">
                    <span class="item-italic">Study Program</span><br>
                    <span class="item-value">{{ $skpi->jurusan->nama_jurusan ?? 'Informatics' }}</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.5 Jenis Pendidikan</span><br>
                    <span class="item-value">Akademik</span>
                </div>
                <div class="column">
                    <span class="item-italic">Classification of Study</span><br>
                    <span class="item-value">Academic</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.6 Jenjang Pendidikan</span><br>
                    <span class="item-value">Strata S-1</span>
                </div>
                <div class="column">
                    <span class="item-italic">Education Level</span><br>
                    <span class="item-value">Bachelor Degree</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.7 Jenjang Kualifikasi Sesuai KKNI</span><br>
                    <span class="item-value">Level 6</span>
                </div>
                <div class="column">
                    <span class="item-italic">Qualification Level of KKNI</span><br>
                    <span class="item-value">Level 6</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.8 Persyaratan Penerimaan</span><br>
                    <span class="item-value">Lulus Pendidikan Menengah Atas atau Sederajat</span>
                </div>
                <div class="column">
                    <span class="item-italic">Admission Requirements</span><br>
                    <span class="item-value">Graduated From High School Or Similar Level Of Education</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.9 Bahasa Pengantar Kuliah</span><br>
                    <span class="item-value">Bahasa Indonesia</span>
                </div>
                <div class="column">
                    <span class="item-italic">Medium of Instruction in Lecture</span><br>
                    <span class="item-value">Indonesian</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.10 Sistem Penilaian</span><br>
                    <span class="item-value">Skala 1-4 ; A : 4.00, A- : 3.75, B+ : 3.50, B : 3.00, B- : 2.75, C+ : 2.50, C : 2.00, D : 1.00, E : 0.00</span>
                </div>
                <div class="column">
                    <span class="item-italic">Evaluation System</span><br>
                    <span class="item-value">Scale 1-4; A: 4.00, A-: 3.75, B+: 3.50, B: 3.00, B-: 2.75, C+: 2.50, C: 2.00, D: 1.00, E: 0.00</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.11 Lama Studi Regular</span><br>
                    <span class="item-value">8 Semester</span>
                </div>
                <div class="column">
                    <span class="item-italic">Regular Study Period</span><br>
                    <span class="item-value">8 Semesters</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.12 Jenis dan Jenjang Pendidikan Lanjutan</span><br>
                    <span class="item-value">Program Magister dan Doktoral</span>
                </div>
                <div class="column">
                    <span class="item-italic">Access to Further Study</span><br>
                    <span class="item-value">Master and Doctoral Program</span>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="two-column">
                <div class="column">
                    <span class="item-label">2.13 Status Profesi</span><br>
                    <span class="item-value">-</span>
                </div>
                <div class="column">
                    <span class="item-italic">Professional Status</span><br>
                    <span class="item-value">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- III. Informasi Kualifikasi dan Hasil yang Dicapai -->
    <div class="section page-break">
        <div class="section-title">
            III. INFORMASI TENTANG KUALIFIKASI DAN HASIL YANG DICAPAI<br>
            <span>INFORMATION OF QUALIFICATION AND LEARNING OUTCOME</span>
        </div>

        <!-- 3.1 Capaian Pembelajaran -->
        <div class="learning-outcomes">
            <div class="item">
                <span class="item-label">3.1 Capaian Pembelajaran</span><br>
                <span class="item-italic">Learning Outcomes</span>
            </div>

            <!-- Sikap / Attitude -->
            <div class="learning-category">
                <div class="learning-category-title">
                    Sikap<br>
                    <span class="item-italic">Attitude</span>
                </div>
                <div class="two-column">
                    <div class="column">
                        <ul class="learning-list">
                            <li>Mampu menunjukkan sikap religius, menjunjung tinggi nilai kemanusiaan dan menghargai keragaman budaya, agama, pandangan atau pendapat orang lain</li>
                            <li>Mampu menunjukkan tindakan nasionalisme serta rasa tanggungjawab pada negara dan bangsa, taat hukum dan disiplin, berkontribusi dalam peningkatan mutu kehidupan bermasyarakat berdasarkan Pancasila</li>
                            <li>Mampu menunjukkan sikap bertanggungjawab atas pekerjaan di bidang keahliannya, serta menginternalisasi nilai, norma, dan etika akademik</li>
                            <li>Mampu menunjukkan kepekaan sosial serta kepedulian terhadap masyarakat di lingkungan dengan semangat kemandirian, kejuangan, dan kewirausahaan</li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul class="learning-list item-italic">
                            <li>Able to show a religious attitude, upholding human values, and respecting the diversity of cultures, religions, views, or opinions of others</li>
                            <li>Able to show actions of nationalism and a sense of responsibility to the state and nation, obey the law and discipline, and contribute to improving the quality of social life based on Pancasila</li>
                            <li>Able to demonstrate a responsible attitude towards work in their field of expertise, as well as internalize academic values, norms, and ethics</li>
                            <li>Able to show social sensitivity and concern for the community in the environment with the spirit of independence, struggle, and entrepreneurship</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kemampuan di Bidang Kerja -->
            <div class="learning-category">
                <div class="learning-category-title">
                    Kemampuan di Bidang Kerja<br>
                    <span class="item-italic">Ability in The Field of Work</span>
                </div>
                <div class="two-column">
                    <div class="column">
                        <ul class="learning-list">
                            <li>Mampu merancang program pemberdayaan sesuai dengan potensi yang ada di masyarakat dan kearifan lokal dengan pendekatan sociopreneur</li>
                            <li>Mampu melakukan analisis, merancang, membuat, dan mengevaluasi perangkat lunak dengan menerapkan prinsip-prinsip sistem cerdas untuk menghasilkan produk aplikasi cerdas pada berbagai bidang</li>
                            <li>Mampu melakukan analisis, merancang, membuat, dan mengelola sistem jaringan komputer yang terdistribusi dan terintegrasi</li>
                            <li>Mampu menerapkan konsep dasar teknologi informasi, arsitektur komputer, prinsip-prinsip kerja sistem operasi untuk merancang, mengimplementasikan dan mengelola/administrasi sistem jaringan cerdas yang mempunyai kinerja tinggi, aman, dan efisien serta menggunakan model, teknik, dan teknologi baru</li>
                            <li>Mampu menerapkan konsep sistem multimedia dan grafika komputer untuk menganalisis, merancang dan menguji purwarupa serta aplikasi multimedia yang sesuai dengan kebutuhan pengguna akhir dengan memanfaatkan teknologi multimedia terkini</li>
                            <li>Mampu menganalisa, merancang, membangun, menguji dan mengimplementasikan rekayasa perangkat lunak dengan menggunakan prinsip-prinsip proses rekayasa perangkat lunak untuk menghasilkan perangkat lunak yang memenuhi kualitas baik secara teknis</li>
                            <li>Mampu menyelesaikan persoalan matematika dan statistik melalui pendekatan eksak, probabilistik dan numerik secara efektif dan efisien untuk data yang besar</li>
                            <li>Mampu merancang dan menganalisa algoritma dan pemrograman untuk menyelesaikan permasalahan secara efektif dan efisien berdasarkan kaidah-kaidah pemrograman yang kuat, serta mampu mengaplikasikan model-model pemrograman yang mendasari berbagai Bahasa pemrograman yang ada, serta mampu memilih bahasa pemrograman untuk menghasilkan aplikasi yang sesuai</li>
                            <li>Mampu mengumpulkan, mengolah, mengekstraksi dan memvisualisasikan data menjadi informasi baru yang lebih bernilai dengan menggunakan pemodelan dan penyimpanan data yang efektif dan efisien</li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul class="learning-list item-italic">
                            <li>Able to design empowerment programs according to the potential that exists in the community and local wisdom with a sociopreneur approach</li>
                            <li>Able to analyze, design, create, and evaluate the software by applying the principles of intelligent systems to produce application products in various fields</li>
                            <li>Able to analyze, design, create, and manage distributed and integrated computer network systems</li>
                            <li>Able to apply the basic concepts of information technology, computer architecture, and operating system working principles to design, implement and manage/administer intelligent network systems that have high performance, security, and efficiency and use new models, techniques and technologies</li>
                            <li>Able to apply the concept of multimedia systems and computer graphics to analyze, design and test prototypes and multimedia applications that suit the needs of end users by utilizing the latest multimedia technology</li>
                            <li>Able to analyze, design, build, test and implement software engineering using software engineering process principles to produce software that meets technically good quality</li>
                            <li>Able to solve mathematical and statistical problems through exact, probabilistic and numerical approaches effectively and efficiently for big data</li>
                            <li>Able to design and analyze algorithms and programming to solve problems effectively and efficiently based on strong programming rules, able to apply programming models that underlie various existing programming languages, and able to choose programming languages to produce appropriate applications</li>
                            <li>Able to collect, process, extract and visualize data into new, more valuable information using effective and efficient data modelling and storage</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pengetahuan yang Dikuasai -->
            <div class="learning-category">
                <div class="learning-category-title">
                    Pengetahuan yang Dikuasai<br>
                    <span class="item-italic">Ability of Knowledge</span>
                </div>
                <div class="two-column">
                    <div class="column">
                        <ul class="learning-list">
                            <li>Mampu menganalisis pengetahuan informatika serta disiplin ilmu lain yang relevan untuk mengidentifikasi solusi</li>
                            <li>Mampu menerapkan teori informatika, keterampilan, teknologi yang sesuai termasuk bahasa pemrograman dan dasar pengembangan perangkat lunak untuk menghasilkan solusi</li>
                            <li>Mampu memecahkan masalah menggunakan model, teknik, dan teknologi baru untuk mempertahankan kompetensi dan meningkatkan kualitas diri</li>
                            <li>Mampu mengkombinasikan berbagai pengetahuan di bidang lain, sehingga mampu berkomunikasi secara efektif dalam tim interdisipliner</li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul class="learning-list item-italic">
                            <li>Able to analyze the knowledge of informatics and other relevant disciplines to identify solutions</li>
                            <li>Able to apply appropriate informatics theory, skills, and technology, including programming languages and software development foundations, to produce solutions</li>
                            <li>Able to solve problems using new models, techniques, and technologies to maintain competence and improve self-quality</li>
                            <li>Able to combine various knowledge in other fields, to communicate effectively in interdisciplinary teams</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kemampuan Umum -->
            <div class="learning-category">
                <div class="learning-category-title">
                    Kemampuan Umum<br>
                    <span class="item-italic">General Skills</span>
                </div>
                <div class="two-column">
                    <div class="column">
                        <ul class="learning-list">
                            <li>Mampu mendefinisikan, merancang, memodelkan, mengimplementasikan, dan mengevaluasi solusi untuk memenuhi kebutuhan pengguna di bidang informatika</li>
                            <li>Mampu mengembangkan diri dan mempertajam kemampuan kerjasama tim secara efektif dalam kegiatan yang sesuai dengan bidang keahliannya</li>
                            <li>Mampu berkomunikasi baik lisan maupun tertulis</li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul class="learning-list item-italic">
                            <li>Able to define, design, model, implement and evaluate solutions to meet users' needs in the field of informatics</li>
                            <li>Able to develop themselves and sharpen teamwork skills effectively in activities that are in their field of expertise</li>
                            <li>Able to communicate both verbally and in writing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3.2 Informasi Tambahan -->
        <div class="additional-info">
            <div class="item">
                <span class="item-label">3.2 Informasi Tambahan</span><br>
                <span class="item-italic">Additional Information</span>
            </div>

            <div class="learning-category">
                <div class="learning-category-title">
                    Penghargaan dan Pemenang Kejuaraan<br>
                    <span class="item-italic">Certificate of Honors and Awards</span>
                </div>
                <div class="two-column">
                    <div class="column">
                        <ul class="additional-list">
                            <li>Staf Ahli Minat Bakat Himpunan Mahasiswa Teknik Informatika (HIMATIF)</li>
                            <li>Peserta dalam kegiatan Big Data Hackathon BPS 2021</li>
                            <li>Direktur Divisi MARCOMM (Marketing Communication) di YOT Bengkulu</li>
                            <li>Studi Komparasi Model Ensemble Deep Learning untuk Menentukan Klasifikasi Jenis Penyu</li>
                            <li>Penyelesaian Program Bangkit dalam Jalur Pembelajaran Machine Learning</li>
                            <li>Penyelesaian UI/UX Designer di Binar Academy</li>
                            <li>Staf IT di PT. Sahabat Professional Indonesia</li>
                            <li>Asisten Laboratorium Mata Kuliah Proyek Sistem Multimedia Semester Ganjil Tahun Akademik 2019/2020</li>
                            <li>Asisten Laboratorium Mata Kuliah Proyek Rekayasa Perangkat Lunak Semester Ganjil Tahun Akademik 2020/2021</li>
                            <li>Asisten Laboratorium Mata Kuliah Proyek Sistem Operasi Semester Ganjil Tahun Akademik 2021/2022</li>
                            <li>Peserta Character Orientation of Informatics Engineering 4</li>
                            <li>Peserta Pelatihan Manajemen Organisasi (PMO)</li>
                            <li>Wisudawan Terbaik Jenjang S1 Periode Ke-100 Universitas Bengkulu</li>
                            <li>Pelatihan Database Design & Programming with SQL</li>
                            <li>Pemegang Hak Cipta Sistem Informasi Klasifikasi Jenis Penyu</li>
                        </ul>
                    </div>
                    <div class="column">
                        <ul class="additional-list item-italic">
                            <li>Talent Interest Expert Staff Of Himpunan Mahasiswa Teknik Informatika (HIMATIF)</li>
                            <li>Participants in the Big Data Hackathon BPS 2021</li>
                            <li>Director Of MARCOMM (Marketing Communication) in YOT Bengkulu</li>
                            <li>Comparative Study of Ensemble Deep Learning Models to Determine Turtle Type Classification</li>
                            <li>Certificate of Completion on Bangkit, specializing in Machine Learning</li>
                            <li>Certificate Of Completion UI/UX Designer at Binar Academy</li>
                            <li>IT Staff at PT. Sahabat Professional Indonesia</li>
                            <li>Laboratory Assistant for Multimedia System Project Courses in the Odd Semester 2019/2020</li>
                            <li>Laboratory Assistant for Software Project Courses in the Odd Semester 2020/2021</li>
                            <li>Laboratory Assistant for Operating System Project Courses in the Odd Semester 2021/2022</li>
                            <li>Participant in the Character Orientation of Informatics Engineering 4</li>
                            <li>Participants in Organizational Management Training (PMO)</li>
                            <li>The Best Bachelor Degree Graduate in The 100th Period of Universitas Bengkulu</li>
                            <li>Database Design & Programming with SQL training</li>
                            <li>The copyright holder of the Turtle Species Classification Information System</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="item">
                <span class="item-label">Pengalaman Organisasi</span><br>
                <span class="item-italic">Organizational Experiences</span> :
                <span class="item-value">-</span>
            </div>

            <div class="item">
                <span class="item-label">Spesifikasi Tugas Akhir</span><br>
                <span class="item-italic">Specification of The Final Project</span> :
                <span class="item-value">-</span>
            </div>

            <div class="item">
                <span class="item-label">Bahasa Internasional</span><br>
                <span class="item-italic">International Language</span> :
                <span class="item-value">-</span>
            </div>

            <div class="item">
                <span class="item-label">Magang Industri</span><br>
                <span class="item-italic">Internship</span> :
                <span class="item-value">-</span>
            </div>

            <div class="item">
                <span class="item-label">Pendidikan Karakter</span><br>
                <span class="item-italic">Soft Skill Training</span> :
                <span class="item-value">-</span>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature">
            <p>Bengkulu, {{ \Carbon\Carbon::parse($skpi->tanggal_lulus)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p>Dekan Fakultas Teknik,</p>
            <div class="signature-name">
                <p>Dr. Eng. Afdhal Kurniawan Mainil, ST, MT</p>
                <p>NIP. 198209262008011007</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dikeluarkan secara resmi oleh Fakultas Teknik Universitas Bengkulu</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY HH:mm') }} WIB</p>
    </div>
</div>