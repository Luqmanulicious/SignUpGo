<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !is_null($registration->evaluations_submitted_at) ? 'View' : 'Evaluate' }} Submission - {{ $event->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, {{ !is_null($registration->evaluations_submitted_at) ? '#6c757d 0%, #495057 100%' : '#667eea 0%, #764ba2 100%' }});
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 2rem;
            padding: 2rem;
        }

        /* Left Side - Product Details */
        .product-section {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .product-section h2 {
            color: #2c3e50;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #667eea;
        }

        .product-title {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .product-meta {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-category {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-theme {
            background: #fef3c7;
            color: #92400e;
        }

        .product-abstract {
            background: white;
            padding: 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            color: #4b5563;
            font-size: 0.95rem;
            border-left: 4px solid #667eea;
        }

        .product-abstract h3 {
            color: #2c3e50;
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }

        .poster-preview {
            margin-top: 1.5rem;
            text-align: center;
        }

        .poster-preview h3 {
            color: #2c3e50;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .poster-preview img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-height: 500px;
            object-fit: contain;
        }

        .video-link {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: #8b5cf6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .video-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* Right Side - Rubric Evaluation */
        .rubric-section {
            padding: 1.5rem;
        }

        .rubric-section h2 {
            color: #2c3e50;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #10b981;
        }

        .rubric-category {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 2px solid #e5e7eb;
        }

        .category-header {
            margin-bottom: 1.5rem;
        }

        .category-name {
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .category-description {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .rubric-item {
            background: white;
            padding: 1.25rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #10b981;
        }

        .rubric-item:last-child {
            margin-bottom: 0;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .item-name {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 600;
            flex: 1;
        }

        .item-description {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .score-selector {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .score-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #4b5563;
            min-width: 60px;
        }

        .score-options {
            display: flex;
            gap: 0.5rem;
        }

        .score-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            background: white;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            color: #6b7280;
        }

        .score-btn:hover {
            border-color: #667eea;
            background: #f0f4ff;
            transform: scale(1.1);
        }

        .score-btn.selected {
            background: #667eea;
            border-color: #667eea;
            color: white;
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .comment-field {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
            transition: border-color 0.2s;
        }

        .comment-field:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .category-comment-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e5e7eb;
        }

        .category-comment-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            display: block;
        }

        /* Submit Section */
        .submit-section {
            background: #f8f9fa;
            padding: 2rem;
            margin-top: 2rem;
            border-radius: 12px;
            text-align: center;
        }

        .submit-btn {
            padding: 1rem 3rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .submit-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .back-btn {
            display: inline-block;
            margin-right: 1rem;
            padding: 1rem 2rem;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1.25rem 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
            max-width: 400px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .toast-icon {
            font-size: 1.5rem;
        }

        /* Score Description Tooltip */
        .score-description-tooltip {
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            line-height: 1.4;
            max-width: 300px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .score-description-tooltip.show {
            opacity: 1;
        }

        .score-description-tooltip::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #1f2937;
        }

        /* Rubric Table Styles */
        .view-rubric-btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .view-rubric-btn:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .view-rubric-btn .arrow {
            transition: transform 0.3s;
        }

        .view-rubric-btn.active .arrow {
            transform: rotate(180deg);
        }

        .rubric-table-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
            margin-top: 1rem;
        }

        .rubric-table-container.active {
            max-height: 1000px;
            transition: max-height 0.5s ease-in;
        }

        .rubric-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            margin-top: 1rem;
        }

        .rubric-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .rubric-table th {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .rubric-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.9rem;
            color: #4b5563;
        }

        .rubric-table tr:last-child td {
            border-bottom: none;
        }

        .rubric-table tr:hover {
            background: #f9fafb;
        }

        .rubric-table .score-column {
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            width: 80px;
            background: #f8f9fa;
        }

        .rubric-table .score-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #667eea;
            color: white;
            border-radius: 50%;
            font-size: 1.1rem;
            font-weight: 700;
            min-width: 45px;
            text-align: center;
        }

        .rubric-table-header {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @media (max-width: 1024px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }

            .product-section {
                position: relative;
                top: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="header">
            <h1>📊 {{ !is_null($registration->evaluations_submitted_at) ? '🔒 View Evaluation (Read-Only)' : (!empty($existingScores) ? 'Edit' : 'Save') . ' Evaluation' }}</h1>
            <p>{{ $event->name }}</p>
            @if(!is_null($registration->evaluations_submitted_at))
                <p style="margin-top: 0.75rem; font-size: 0.9rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 6px; display: inline-block;">
                    ✅ Finalized on {{ \Carbon\Carbon::parse($registration->evaluations_submitted_at)->format('M d, Y h:i A') }}
                </p>
            @endif
        </div>
        <a href="{{ route('events.jury-dashboard', $registration->id) }}" class="back-btn">
            ← Back to Jury Dashboard
        </a>
        
        @if(!is_null($registration->evaluations_submitted_at))
            <div style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white; padding: 1rem 2rem; margin: 1rem 2rem; border-radius: 8px; text-align: center; font-weight: 600;">
                🔒 This evaluation has been finalized and submitted. All fields are read-only.
            </div>
        @endif
        <div class="content-wrapper">
            <!-- Left Side: Product Details -->
            <div class="product-section">
                <h2>📄 Product Details</h2>

                <div class="product-title">
                    {{ $paper->title }}
                </div>

                <div class="product-meta">
                    @if ($paper->product_category)
                        <span class="badge badge-category">{{ $paper->product_category }}</span>
                    @endif
                    @if ($paper->product_theme)
                        <span class="badge badge-theme">{{ $paper->product_theme }}</span>
                    @endif
                </div>

                @if ($paper->abstract)
                    <div class="product-abstract">
                        <h3>Abstract</h3>
                        <p>{{ $paper->abstract }}</p>
                    </div>
                @endif

                @if ($paper->poster_path)
                    <div class="poster-preview">
                        <h3>📋 Poster</h3>
                        <img src="{{ $paper->poster_path }}" alt="Product Poster">
                    </div>
                @endif

                @if ($paper->video_url)
                    <a href="{{ $paper->video_url }}" target="_blank" class="video-link">
                        🎥 Watch Video Presentation
                    </a>
                @endif
            </div>

            <!-- Right Side: Rubric Evaluation -->
            <div class="rubric-section">
                <h2>✅ Rubric Evaluation</h2>

                <form id="evaluationForm" method="POST"
                    action="{{ route('jury.evaluate.submit', $juryMapping->id) }}">
                    @csrf

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">
                            <ul style="margin: 0; padding-left: 1.5rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @foreach ($rubricCategories as $category)
                        <div class="rubric-category">
                            <div class="category-header">
                                <div class="category-name">{{ $category->name }}</div>
                                @if ($category->description)
                                    <div class="category-description">{{ $category->description }}</div>
                                @endif
                            </div>

                            @foreach ($category->items as $item)
                                <div class="rubric-item">
                                    <div class="item-header">
                                        <div class="item-name">{{ $item->name }}</div>
                                    </div>

                                    @if ($item->description)
                                        <div class="item-description">{{ $item->description }}</div>
                                    @endif

                                    <div class="score-selector">
                                        <div class="score-label">Score:</div>
                                        <div class="score-options">
                                            @for ($i = 0; $i <= ($item->max_score ?? 5); $i++)
                                                <button type="button" class="score-btn {{ isset($existingScores[$item->id]) && $existingScores[$item->id] == $i ? 'selected' : '' }}"
                                                    data-item-id="{{ $item->id }}"
                                                    data-score="{{ $i }}"
                                                    onclick="selectScore({{ $item->id }}, {{ $i }})"
                                                    {{ !is_null($registration->evaluations_submitted_at) ? 'disabled style="cursor: not-allowed; opacity: 0.6;"' : '' }}>
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </div>
                                    </div>

                                    <input type="hidden" name="scores[{{ $item->id }}]"
                                        id="score_{{ $item->id }}" value="{{ $existingScores[$item->id] ?? '' }}" required>

                                    <!-- Score Level Descriptions (hidden, for tooltip) -->
                                    <div id="descriptions_{{ $item->id }}" style="display: none;">
                                        @php
                                            $scoreLevels = DB::table('rubric_score_levels')
                                                ->where('rubric_item_id', $item->id)
                                                ->orderBy('level')
                                                ->get();
                                        @endphp
                                        @foreach($scoreLevels as $level)
                                            <div data-level="{{ $level->level }}">{{ $level->description }}</div>
                                        @endforeach
                                    </div>

                                    <!-- View Rubric Table Button -->
                                    <button type="button" class="view-rubric-btn" onclick="toggleRubricTable({{ $item->id }})">
                                        📋 View Rubric Table
                                        <span class="arrow">▼</span>
                                    </button>

                                    <!-- Rubric Table (Initially Hidden) -->
                                    <div class="rubric-table-container" id="rubric_table_{{ $item->id }}">
                                        <div class="rubric-table">
                                            <div class="rubric-table-header">
                                                📊 Score Levels for "{{ $item->name }}"
                                            </div>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th style="width: 100px; text-align: center;">Score</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $scoreLevelsTable = DB::table('rubric_score_levels')
                                                            ->where('rubric_item_id', $item->id)
                                                            ->orderBy('level', 'desc')
                                                            ->get();
                                                    @endphp
                                                    @if($scoreLevelsTable->count() > 0)
                                                        @foreach($scoreLevelsTable as $level)
                                                            <tr>
                                                                <td class="score-column">
                                                                    <span class="score-badge">{{ $level->level }}</span>
                                                                </td>
                                                                <td>{{ $level->description }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="2" style="text-align: center; color: #9ca3af; padding: 2rem;">
                                                                No score level descriptions available
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="category-comment-section">
                                <label class="category-comment-label">Overall Comments for {{ $category->name }} (Optional)</label>
                                <textarea name="category_comments[{{ $category->id }}]" class="comment-field"
                                    placeholder="Add overall comments for {{ $category->name }}" id="category_comment_{{ $category->id }}"
                                    {{ !is_null($registration->evaluations_submitted_at) ? 'readonly style="background: #f5f5f5; cursor: not-allowed;"' : '' }}>{{ $categoryComments[$category->id] ?? '' }}</textarea>
                            </div>
                        </div>
                    @endforeach

                    @if(is_null($registration->evaluations_submitted_at))
                        <div class="submit-section">
                            <button type="submit" class="submit-btn" id="submitBtn">
                                ✓ Save Evaluation
                            </button>
                            <p style="color: #6c757d;">Please submit all evaluations via the Jury Dashboard only after you have finalized your scores.</p>
                        </div>
                    @else
                        <div class="submit-section">
                            <div style="padding: 1.5rem; background: #f8f9fa; border-radius: 8px; text-align: center; color: #6c757d; font-weight: 600;">
                                🔒 This evaluation has been finalized and cannot be modified.
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            
            const icon = type === 'success' ? '✓' : '✗';
            toast.innerHTML = `
                <span class="toast-icon">${icon}</span>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 4000);
        }

        // Show success/error notifications
        @if (session('success'))
            window.addEventListener('load', function() {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if (session('error'))
            window.addEventListener('load', function() {
                showToast('{{ session('error') }}', 'error');
            });
        @endif

        @if ($errors->any())
            window.addEventListener('load', function() {
                showToast('{{ $errors->first() }}', 'error');
            });
        @endif

        let currentTooltip = null;

        // Toggle rubric table visibility
        function toggleRubricTable(itemId) {
            const table = document.getElementById(`rubric_table_${itemId}`);
            const button = event.target.closest('.view-rubric-btn');
            
            if (table.classList.contains('active')) {
                table.classList.remove('active');
                button.classList.remove('active');
                button.querySelector('.arrow').textContent = '▼';
                button.innerHTML = button.innerHTML.replace('Hide Rubric Table', 'View Rubric Table');
            } else {
                table.classList.add('active');
                button.classList.add('active');
                button.querySelector('.arrow').textContent = '▲';
                button.innerHTML = button.innerHTML.replace('View Rubric Table', 'Hide Rubric Table');
            }
        }

        function selectScore(itemId, score) {
            // Remove selected class from all buttons for this item
            const buttons = document.querySelectorAll(`.score-btn[data-item-id="${itemId}"]`);
            buttons.forEach(btn => btn.classList.remove('selected'));

            // Add selected class to clicked button
            const clickedBtn = document.querySelector(`.score-btn[data-item-id="${itemId}"][data-score="${score}"]`);
            clickedBtn.classList.add('selected');

            // Update hidden input
            document.getElementById(`score_${itemId}`).value = score;
        }

        function showScoreDescription(itemId, score, button) {
            // Remove existing tooltip
            if (currentTooltip) {
                currentTooltip.remove();
            }

            // Get description for this score level
            const descriptionsDiv = document.getElementById(`descriptions_${itemId}`);
            if (!descriptionsDiv) return;

            const descriptionEl = descriptionsDiv.querySelector(`[data-level="${score}"]`);
            if (!descriptionEl) return;

            const description = descriptionEl.textContent;
            if (!description) return;

            // Create tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'score-description-tooltip show';
            tooltip.textContent = description;

            document.body.appendChild(tooltip);
            currentTooltip = tooltip;

            // Position tooltip
            const rect = button.getBoundingClientRect();
            tooltip.style.position = 'fixed';
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.bottom + 10 + 'px';
        }

        function hideScoreDescription() {
            if (currentTooltip) {
                currentTooltip.classList.remove('show');
                setTimeout(() => {
                    if (currentTooltip) {
                        currentTooltip.remove();
                        currentTooltip = null;
                    }
                }, 200);
            }
        }

        // Add hover events to score buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.score-btn').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const score = this.getAttribute('data-score');
                    showScoreDescription(itemId, score, this);
                });
                btn.addEventListener('mouseleave', hideScoreDescription);
            });
        });

        // Form validation before submit
        @if(is_null($registration->evaluations_submitted_at))
            document.getElementById('evaluationForm').addEventListener('submit', function(e) {
                const allItems = document.querySelectorAll('input[name^="scores"]');
                let allScored = true;

                allItems.forEach(input => {
                    if (!input.value && input.value !== '0') {
                        allScored = false;
                    }
                });

                if (!allScored) {
                    e.preventDefault();
                    showToast('Please provide scores for all rubric items before submitting.', 'error');
                    return false;
                }

                // Disable submit button to prevent double submission
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
            });
        @else
            // Prevent form submission if evaluations are finalized
            document.getElementById('evaluationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                showToast('This evaluation has been finalized and cannot be modified.', 'error');
                return false;
            });
            
            // Disable score selection for read-only mode
            function selectScore(itemId, score) {
                // Do nothing in read-only mode
                return false;
            }
        @endif
    </script>
</body>

</html>
