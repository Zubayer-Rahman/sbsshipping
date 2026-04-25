# Project Structure

```
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── AuthController.php
│   │   │   ├── ContactController.php
│   │   │   ├── Controller.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ExpenseCategoryController.php
│   │   │   ├── ExpenseController.php
│   │   │   ├── ForwardingController.php
│   │   │   ├── ItemController.php
│   │   │   ├── JobController.php
│   │   │   └── PurchaseController.php
│   │   └── ContactRequest.php
│   ├── Models
│   │   ├── Contact.php
│   │   ├── Expense.php
│   │   ├── ExpenseCategory.php
│   │   ├── ForwardingLetter.php
│   │   ├── Item.php
│   │   ├── Job.php
│   │   ├── Purchase.php
│   │   ├── PurchaseItem.php
│   │   └── User.php
│   └── Providers
│       └── AppServiceProvider.php
├── bootstrap
│   ├── cache
│   │   ├── packages.php
│   │   └── services.php
│   ├── app.php
│   └── providers.php
├── config
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
├── database
│   ├── factories
│   │   └── UserFactory.php
│   ├── migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_02_000001_add_columns_to_jobs_table.php
│   │   ├── 2024_01_03_000001_add_job_form_fields_to_jobs_table.php
│   │   ├── 2024_01_04_000001_add_timestamps_to_jobs_table.php
│   │   ├── 2024_01_05_000001_rename_jobs_to_shipping_jobs.php
│   │   ├── 2024_01_06_000001_create_sbs_jobs_table.php
│   │   ├── 2024_01_07_000001_create_items_table.php
│   │   ├── 2024_01_08_000001_add_stock_columns_to_items_table.php
│   │   ├── 2024_01_09_000001_create_forwarding_letters_table.php
│   │   ├── 2024_01_10_000001_create_expense_categories_table.php
│   │   ├── 2024_01_10_000002_create_expenses_table.php
│   │   ├── 2024_01_11_000001_create_purchases_tables.php
│   │   ├── 2025_04_01_000001_create_contacts_table.php
│   │   └── 2025_04_01_000002_fix_contacts_name_column.php
│   ├── seeders
│   │   └── DatabaseSeeder.php
│   └── database.sqlite
├── public
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── resources
│   ├── css
│   │   └── app.css
│   ├── js
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views
│       ├── auth
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── contacts
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── dashboard
│       │   └── index.blade.php
│       ├── expenses
│       │   ├── categories.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── list.blade.php
│       │   ├── purchase.blade.php
│       │   ├── PurchaseCreate.blade.php
│       │   ├── PurchaseList.blade.php
│       │   └── PurchaseShow.blade.php
│       ├── items
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── list.blade.php
│       ├── jobs
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── forwarding_list.blade.php
│       │   ├── forwarding_preview.blade.php
│       │   ├── forwarding.blade.php
│       │   ├── list.blade.php
│       │   └── show.blade.php
│       └── layouts
│           └── app.blade.php
├── routes
│   ├── console.php
│   └── web.php
├── storage
│   ├── app
│   │   ├── private
│   │   └── public
│   │       └── expense_docs
│   │           └── FwzUPGhocQpqWalMe6CwK6jghghbpE91hzRN4BEw.pdf
│   ├── framework
│   │   ├── cache
│   │   │   └── data
│   │   ├── sessions
│   │   ├── testing
│   │   └── views
│   │       ├── 080ac72c012f5c90f83859a9d91cc22a.php
│   │       ├── 0982e5edcff8df1bc42a2a3da70e6f05.php
│   │       ├── 0c09ede1eea777bb1a34569243f2a6a6.php
│   │       ├── 1288a73502c191fdc367a0b28cd8b03e.php
│   │       ├── 12f6dd32736c36bab864fa13513b9e53.php
│   │       ├── 140d674e7e0b4eaf9f7976a0ce0f473e.php
│   │       ├── 17fa2b1fcd2d872e9ae06e4ffe6f36b9.php
│   │       ├── 181e271e1ed3716ef3001b46facc0395.php
│   │       ├── 19602160d68f901ebb34d2b5ec4b6944.php
│   │       ├── 23321dbbe50b496f5afc58de04f95d23.php
│   │       ├── 23760ce87ec91370d1c70240987368c2.php
│   │       ├── 2dad08b0d2b673fc775a143886378570.php
│   │       ├── 2ece00caee7aa204090fdd9603453560.php
│   │       ├── 32388a3a35348e7ff849d6d6ce14aade.php
│   │       ├── 393d41fe28a9db66ea8394ec0936ab61.php
│   │       ├── 3aacb7d36779b14c8c6a4af21fdcdb8a.php
│   │       ├── 3b5e3c667881ff82f5e0f90041522ef7.php
│   │       ├── 545ea656b7954079edadf6c51895742e.php
│   │       ├── 577c14ce106a2dd7a187140b45c65555.php
│   │       ├── 59ecfaaa5843cc2b958a5c31b90e770b.php
│   │       ├── 5c728b110c3fdd92575f7aceeb008969.php
│   │       ├── 62ba26fab100fb998c6853adecd612e9.php
│   │       ├── 63a772db0f0af070bed8b240170a4360.php
│   │       ├── 68e60840513a8137735d97ae93a5e423.php
│   │       ├── 7865ff7f0a783ed6bcd4371b96646411.php
│   │       ├── 80ef1f9b9ee7bb99ce0df6bff0fb55dd.php
│   │       ├── 837369395a89dd28d46528ffe45c60b6.php
│   │       ├── 849e76408e048432d99793222e1148dc.php
│   │       ├── 85eea2c4dde96da11e35e3faf11b7e1c.php
│   │       ├── 887707c3ad58d5effc4df6e74372f4f1.php
│   │       ├── 89286c39cbdda00c6293fc9b974edb42.php
│   │       ├── 8b464bfcad233ca1eb5efa97e3cac61b.php
│   │       ├── 92944df15f6ba298866da0b37bb03bd4.php
│   │       ├── 93be35893e09aa1bc67541ea79ebfe9d.php
│   │       ├── 940d7cb4262f0fdc45ff44a04dd52225.php
│   │       ├── 96e9dd403366748e9629cfd39e5196c0.php
│   │       ├── 99308f9ee697e5cdc28545d89669ee9c.php
│   │       ├── 9b9575a498131ec0883f2daf145da20c.php
│   │       ├── a0d770b8d71d9ac5b7170727f798325c.php
│   │       ├── a3752488f834521af39c04c85ecd7958.php
│   │       ├── a75dff11f49ae22c0265733a0294ac9d.php
│   │       ├── ab020d2427e5bfe53e7152b3b929eb9f.php
│   │       ├── af28243f4a1b0b007d7268f754185e73.php
│   │       ├── b24c750558f8d747eb83a3891aef7e67.php
│   │       ├── b459056bc9cb7d2bb04c9af44ce5bb58.php
│   │       ├── b7c3b5a247c26a00a5f12d866209a625.php
│   │       ├── b9b67df604d7353cd53ce1d93b4cf562.php
│   │       ├── b9decce9c3adcb731726f6ace7151873.php
│   │       ├── b9ff284e562e55de00cbeb4f7a2a5762.php
│   │       ├── bd3037567a9958c991103a7614f6b33c.php
│   │       ├── c0a757c56b073077e0acb3ec22294739.php
│   │       ├── c1708d88ad0b47a895877fd35b1a8da5.php
│   │       ├── c4b590f81d8292bfeb4e46891fd5ec4f.php
│   │       ├── c5715048bb495604500de0c902ff56ac.php
│   │       ├── c92d721eddcf705c307f4860e53fddd1.php
│   │       ├── ccc4f27ca2de7be6a97024d3920d7408.php
│   │       ├── d5acfedeed092d7f53d0f40b9d9b5aac.php
│   │       ├── db5b65070ac0cd7b4db51de061cb8733.php
│   │       ├── e3872de6d88d42ea255e029963d9e8f8.php
│   │       ├── e3df9ebe7b50f80063825ef8a2017c63.php
│   │       ├── e6b7738210c5464d15b467d2d527ac00.php
│   │       ├── ec8f8cb5bc9dbdd966cff14ab9ca58b3.php
│   │       ├── ed24121d6c77fe1e7e89e10093494ade.php
│   │       ├── f3ea3a3cd752c383cf4f6d4443902afe.php
│   │       ├── f8ec428e724ad68c3750d674bb8e9561.php
│   │       ├── fa0e594e0e56d65e7d6185a07d9610d9.php
│   │       └── fc703f6974ba3e2d37a8ac12de63dec3.php
│   └── logs
├── tests
│   ├── Feature
│   │   └── ExampleTest.php
│   ├── Unit
│   │   └── ExampleTest.php
│   └── TestCase.php
├── artisan
├── CHANGELOG.md
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── README.md
└── vite.config.js
```
