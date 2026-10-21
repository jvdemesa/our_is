import sys
import json
from collections import defaultdict


# =========================================================
# CHECK CONFLICT
# =========================================================

def has_conflict(sec_id, assigned_sections, conflict_map):

    for assigned_sec in assigned_sections:
        if sec_id in conflict_map[assigned_sec]:
            return True

    return False


# =========================================================
# GREEDY SOLVER
# =========================================================

def solve_assignment(data):

    requirements = data["students"]

    # =====================================================
    # SECTIONS
    # =====================================================

    sections = {
        int(k): v
        for k, v in data["sections"].items()
    }

    for sec_id in sections:
        sections[sec_id]["remaining"] = int(
            sections[sec_id]["max_capacity"]
        )

    # =====================================================
    # CONFLICT MAP
    # =====================================================

    conflicts = [
        (int(a), int(b))
        for a, b in data["conflicts"]
    ]

    conflict_map = defaultdict(set)

    for a, b in conflicts:
        conflict_map[a].add(b)
        conflict_map[b].add(a)

    # =====================================================
    # GROUP STUDENTS
    # =====================================================

    students = {}

    for req in requirements:

        student_no = req["student_no"]

        if student_no not in students:

            students[student_no] = {
                "student_name": req["name"],
                "max_units": req["max_units"],
                "subjects": []
            }

        students[student_no]["subjects"].append(
            req["subject_code"]
        )

    # =====================================================
    # SUBJECT -> SECTION LOOKUP
    # =====================================================

    subject_sections = defaultdict(list)
    subject_is_gi = {}

    for sec_id, sec in sections.items():

        subject = sec["subject"].strip().upper()

        subject_sections[subject].append(sec_id)

        subject_is_gi[subject] = int(sec["is_gi"])

    # =====================================================
    # SORT STUDENTS (hard first)
    # =====================================================

    sorted_students = sorted(
        students.items(),
        key=lambda x: len(x[1]["subjects"]),
        reverse=True
    )

    # =====================================================
    # RESULT STORAGE
    # =====================================================

    solution = {}

    # =====================================================
    # GREEDY ASSIGNMENT
    # =====================================================

    for student_no, student in sorted_students:

        assigned_sections = []
        solution[student_no] = []

        sorted_subjects = sorted(
            student["subjects"],
            key=lambda s: (
                subject_is_gi.get(s.strip().upper(), 1),
                len(subject_sections.get(s.strip().upper(), []))
            )
        )

        for subject_code in sorted_subjects:

            subject_key = subject_code.strip().upper()

            possible_sections = subject_sections.get(
                subject_key,
                []
            )

            possible_sections = sorted(
                possible_sections,
                key=lambda sec_id: sections[sec_id]["remaining"],
                reverse=True
            )

            assigned = False
            reason_full = True
            reason_conflict = True

            for sec_id in possible_sections:

                # CAPACITY CHECK
                if sections[sec_id]["remaining"] <= 0:
                    continue
                else:
                    reason_full = False

                # CONFLICT CHECK
                if has_conflict(sec_id, assigned_sections, conflict_map):
                    continue
                else:
                    reason_conflict = False

                # ASSIGN
                assigned_sections.append(sec_id)
                sections[sec_id]["remaining"] -= 1

                solution[student_no].append({

                    "student_no": student_no,
                    "student_name": student["student_name"],
                    "subject_code": subject_code,

                    "section_id": sec_id,
                    "section_name": sections[sec_id]["section_name"],
                    "subject": sections[sec_id]["subject"],

                    "days": sections[sec_id]["days"],
                    "assigned": 1,
                    "message": "assigned"
                })

                assigned = True
                break

            # =================================================
            # FAILURE REASONING
            # =================================================

            if not assigned:

                if reason_full and reason_conflict:
                    message = "No available section (full + conflict)"
                elif reason_full:
                    message = "All sections are full"
                elif reason_conflict:
                    message = "All sections conflict with schedule"
                else:
                    message = "No valid section"

                solution[student_no].append({

                    "student_no": student_no,
                    "student_name": student["student_name"],
                    "subject_code": subject_code,
                    "assigned": 0,
                    "message": message
                })

    # =====================================================
    # SECTION STATISTICS (MOVED OUTSIDE LOOP FIXED)
    # =====================================================

    section_statistics = []

    for sec_id, sec in sections.items():

        total_slots = int(sec["max_capacity"])
        remaining_slots = int(sec["remaining"])
        used_slots = total_slots - remaining_slots

        section_statistics.append({

            "section_id": sec_id,
            "subject_code": sec["subject"],
            "section_name": sec["section_name"],
            "is_gi": sec["is_gi"],

            "total_slots": total_slots,
            "used_slots": used_slots,
            "remaining_slots": remaining_slots,

            "days": sec["days"]
        })

    # =====================================================
    # FINAL OUTPUT
    # =====================================================

    return {

        "students": solution,
        "section_statistics": section_statistics
    }


# =========================================================
# MAIN
# =========================================================

if __name__ == "__main__":

    raw = sys.stdin.read()
    data = json.loads(raw)

    result = solve_assignment(data)

    print(json.dumps(result, indent=2))