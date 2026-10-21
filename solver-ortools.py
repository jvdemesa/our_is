import sys
import json
from ortools.linear_solver import pywraplp


def solve_assignment(data):

    requirements = data["students"]

    # Convert section keys to int
    sections = {int(k): v for k, v in data["sections"].items()}

    # Normalize conflicts
    conflicts = [(int(a), int(b)) for a, b in data["conflicts"]]

    from collections import defaultdict

    # Fast conflict lookup
    conflict_map = defaultdict(set)

    for a, b in conflicts:
        conflict_map[a].add(b)
        conflict_map[b].add(a)

    solver = pywraplp.Solver.CreateSolver("SCIP")

    if not solver:
        return {"error": "SCIP solver not available"}

    # =========================================================
    # DECISION VARIABLES
    # x[r_idx, sec_id] = 1 if requirement r_idx assigned to sec_id
    # =========================================================

    x = {}

    for r_idx, req in enumerate(requirements):

        req_subject = req["subject_code"].strip().upper()

        for sec_id, sec in sections.items():

            sec_subject = sec["subject"].strip().upper()

            # ONLY create variable if section belongs to subject
            if req_subject == sec_subject:

                x[r_idx, sec_id] = solver.IntVar(
                    0,
                    1,
                    f"x[{r_idx},{sec_id}]"
                )

    # =========================================================
    # EACH SUBJECT REQUIREMENT CAN ONLY HAVE ONE SECTION
    # =========================================================

    for r_idx, req in enumerate(requirements):

        subject_sections = []

        for sec_id, sec in sections.items():

            if (
                sec["subject"].strip().upper()
                == req["subject_code"].strip().upper()
            ):

                if (r_idx, sec_id) in x:
                    subject_sections.append(x[r_idx, sec_id])

        # <= 1 allows best-fit schedule
        # use == 1 if you want strict assignment
        if subject_sections:
            solver.Add(sum(subject_sections) <= 1)

    # =========================================================
    # SECTION CAPACITY
    # =========================================================

    for sec_id, sec in sections.items():

        section_vars = []

        for r_idx in range(len(requirements)):

            if (r_idx, sec_id) in x:
                section_vars.append(x[r_idx, sec_id])

        solver.Add(
            sum(section_vars)
            <= int(sec["max_capacity"])
        )

    # =========================================================
    # GROUP REQUIREMENTS BY STUDENT
    # =========================================================

    student_reqs = {}

    for r_idx, req in enumerate(requirements):

        student_no = req["student_no"]

        if student_no not in student_reqs:
            student_reqs[student_no] = []

        student_reqs[student_no].append(r_idx)

    # =========================================================
    # CONFLICT CONSTRAINTS
    # STUDENT CANNOT TAKE OVERLAPPING SECTIONS
    # =========================================================

    for student_no, req_indices in student_reqs.items():

        student_section_vars = []

        for r_idx in req_indices:

            for sec_id in sections.keys():

                if (r_idx, sec_id) in x:
                    student_section_vars.append((sec_id, x[r_idx, sec_id]))

        # compare only assigned-capable sections
        for i in range(len(student_section_vars)):

            secA, varA = student_section_vars[i]

            for j in range(i + 1, len(student_section_vars)):

                secB, varB = student_section_vars[j]

                if secB in conflict_map[secA]:

                    solver.Add(varA + varB <= 1)

    # =========================================================
    # OPTIONAL: MAX UNITS
    # =========================================================

    # Example if sections contain "units"
    #
    # for student_no, req_indices in student_reqs.items():
    #
    #     max_units = max(
    #         int(req["max_units"])
    #         for req in requirements
    #         if req["student_no"] == student_no
    #     )
    #
    #     unit_expr = []
    #
    #     for r_idx in req_indices:
    #
    #         for sec_id, sec in sections.items():
    #
    #             if (r_idx, sec_id) in x:
    #
    #                 units = int(sec.get("units", 3))
    #
    #                 unit_expr.append(
    #                     x[r_idx, sec_id] * units
    #                 )
    #
    #     solver.Add(sum(unit_expr) <= max_units)

    # =========================================================
    # OBJECTIVE
    # MAXIMIZE NUMBER OF ASSIGNED SUBJECTS
    # =========================================================

    solver.Maximize(sum(x.values()))

    # =========================================================
    # SOLVE
    # =========================================================

    status = solver.Solve()

    # =========================================================
    # OUTPUT
    # =========================================================

    if status == pywraplp.Solver.OPTIMAL:

        solution = {}

        for r_idx, req in enumerate(requirements):

            for sec_id in sections.keys():

                if (r_idx, sec_id) not in x:
                    continue

                if x[r_idx, sec_id].solution_value() > 0.5:

                    solution.setdefault(
                        req["student_no"],
                        []
                    ).append({

                        "student_no": req["student_no"],
                        "student_name": req["name"],

                        "subject_code": req["subject_code"],

                        "section_id": sec_id,

                        "section_name":
                            sections[sec_id]["section_name"],

                        "subject":
                            sections[sec_id]["subject"],

                        "days":
                            sections[sec_id]["days"]
                    })

        return solution

    else:

        return {
            "error": "No feasible solution"
        }


# =========================================================
# MAIN
# =========================================================

if __name__ == "__main__":

    raw = sys.stdin.read()

    data = json.loads(raw)

    result = solve_assignment(data)

    print(json.dumps(result, indent=2))