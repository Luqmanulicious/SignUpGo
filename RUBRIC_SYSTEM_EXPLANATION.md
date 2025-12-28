# Rubric Evaluation System - Table Relationships

## Overview
The rubric system now uses the correct tables that link evaluations to specific participants and events.

## Database Tables

### 1. **rubric_categories**
Stores rubric categories for each event (e.g., "Poster")
- `id` - Primary key
- `event_id` - Links to specific event
- `name` - Category name (e.g., "Poster")
- `description` - Category description
- `max_score` - Maximum score for this category
- `weight` - Percentage weight of this category
- `order` - Display order
- `is_active` - Whether this category is active

### 2. **rubric_items**
Stores individual evaluation criteria within each category
- `id` - Primary key
- `rubric_category_id` - Links to rubric_categories
- `name` - Criterion name (e.g., "Abstract", "Method")
- `description` - Description of what to evaluate
- `max_score` - Maximum score (usually 5)
- `order` - Display order within category
- `is_active` - Whether this item is active

### 3. **rubric_score_levels**
Stores descriptions for each score level (what each 1-5 means)
- `id` - Primary key
- `rubric_item_id` - Links to rubric_items
- `level` - Score level (1, 2, 3, 4, or 5)
- `description` - What this level means for this criterion

### 4. **rubric_item_scores** (NEW - Created today)
**Stores the actual scores given by reviewers**
- `id` - Primary key
- `jury_mapping_id` - Links to jury_mappings (which reviewer → which participant)
- `rubric_item_id` - Which criterion was scored
- `event_paper_id` - Links to the participant's paper (nullable)
- `evaluator_id` - The user ID of the reviewer
- `score` - The actual score (1-5)
- `comment` - Optional comments
- `created_at`, `updated_at` - Timestamps

**Unique constraint:** `(jury_mapping_id, rubric_item_id)` - One score per reviewer per criterion per assignment

### 5. **jury_mappings** (Existing)
Links reviewers to participants for specific events
- `id` - Primary key
- `event_id` - Which event
- `reviewer_registration_id` - The reviewer's registration
- `participant_registration_id` - The participant's registration
- `score` - Overall percentage score (calculated from rubric_item_scores)
- `review_notes` - Additional notes
- `status` - pending/completed
- `reviewed_at` - When review was submitted

## How It All Connects

```
Event (id: 1)
    └── rubric_categories (event_id: 1)
            └── "Poster" (id: 1, max_score: 30, weight: 100%)
                    ├── rubric_items (rubric_category_id: 1)
                    │       ├── "Abstract" (id: 1, max_score: 5)
                    │       │       └── rubric_score_levels (rubric_item_id: 1)
                    │       │               ├── Level 1: "Only contains 1 required item"
                    │       │               ├── Level 2: "Only contains 2 required items"
                    │       │               └── ... etc
                    │       ├── "Problem Statement" (id: 2, max_score: 5)
                    │       └── "Method" (id: 3, max_score: 5)
                    │
                    └── When Reviewer Evaluates:
                            jury_mappings (id: 100)
                                ├── event_id: 1
                                ├── reviewer_registration_id: 50
                                ├── participant_registration_id: 75
                                └── score: 87 (calculated percentage)

                            rubric_item_scores:
                                ├── (jury_mapping_id: 100, rubric_item_id: 1, score: 4)
                                ├── (jury_mapping_id: 100, rubric_item_id: 2, score: 5)
                                └── (jury_mapping_id: 100, rubric_item_id: 3, score: 4)
                                
                            Calculation:
                                Total: 4 + 5 + 4 = 13
                                Max: 5 + 5 + 5 = 15
                                Percentage: (13/15) * 100 = 87%
```

## How It Answers Your Question

**Your Question:** "How will the score know which specific participant for which specific event?"

**Answer:**
1. **Event Linkage:** 
   - `rubric_categories.event_id` links categories to events
   - `jury_mappings.event_id` ensures the assignment is for a specific event

2. **Participant Linkage:**
   - `jury_mappings.participant_registration_id` identifies the specific participant
   - `rubric_item_scores.jury_mapping_id` links each score to that specific reviewer-participant assignment
   - `rubric_item_scores.event_paper_id` (optional) directly links to the participant's paper

3. **Reviewer Linkage:**
   - `jury_mappings.reviewer_registration_id` identifies who is doing the review
   - `rubric_item_scores.evaluator_id` double-confirms who gave the score

## Flow When Reviewer Submits:

1. Reviewer is on Event Dashboard for Event #1
2. They see participants assigned to them via `jury_mappings`
3. They click "Review" for Participant A
4. Modal loads rubric items from `rubric_items` (filtered by event's categories)
5. Reviewer selects scores (1-5) for each item
6. On submit:
   - For each rubric item: Insert into `rubric_item_scores` with:
     * `jury_mapping_id` → Links to specific assignment
     * `rubric_item_id` → Which criterion
     * `score` → The 1-5 value
     * `evaluator_id` → Current user
     * `event_paper_id` → Participant's paper (if exists)
   - Calculate total percentage
   - Update `jury_mappings.score` with percentage
   - Update `jury_mappings.status` to 'completed'

## Benefits of This Structure:

✅ **Event-specific:** Rubrics are defined per event via `rubric_categories.event_id`  
✅ **Participant-specific:** Scores linked via `jury_mapping_id` which contains participant info  
✅ **Reusable:** Same rubric items can be used across multiple events  
✅ **Detailed tracking:** Individual item scores stored separately  
✅ **Flexible:** Can have different categories with different weights  
✅ **Prevents duplicates:** Unique constraint ensures one score per reviewer per item  
✅ **Audit trail:** Knows who scored what and when via timestamps and evaluator_id
