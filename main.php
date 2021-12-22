<HTML>

<BODY>
<canvas id="hw_07" width="500" height="400" style="border: 1px solid">
</canvas>
<script>
    function Line(ctx, x_start, y_start, x_end, y_end, color){
        ctx.fillStyle = color;
        let d_max = Math.max(Math.abs(x_end - x_start), Math.abs(y_end - y_start));
        let d_min = Math.min(Math.abs(x_end - x_start), Math.abs(y_end - y_start));
        let x_dir = 1;
        if (x_end < x_start) x_dir = -1;
        let y_dir = 1;
        if (y_end < y_start) y_dir = -1;
        let eps = 0;
        let s = 1;
        let k = 2 * d_min;
        if (Math.abs(y_end - y_start) <= Math.abs(x_end - x_start)) {
            let y = y_start;
            for (let x = x_start; x * x_dir <= x_end * x_dir; x += x_dir) {
                ctx.fillRect(x * s, y * s, s, s);
                eps = eps + k;
                if (eps > d_max) {
                    y += y_dir;
                    eps = eps - 2 * d_max;
                }
            }
        } else {
            let x = x_start;
            for (let y = y_start; y * y_dir <= y_end * y_dir; y += y_dir) {
                ctx.fillRect(x * s, y * s, s, s);
                eps = eps + k;
                if (eps > d_max) {
                    x += x_dir;
                    eps = eps - 2 * d_max;
                }
            }
        }
    }

    function distance(p0, p1, p2) {
        const k = (p2[1] - p0[1]) / (p2[0] - p0[0]);
        const b = -1 * k * p0[0] + p0[1];
        return Math.abs(-k * p1[0] + p1[1] - 1 * b) / Math.sqrt(k * k + 1)
    }

    function Bezie_line(p0, p1, p2) {
        if (distance(p0, p1, p2) > 1) {
            const p0_1 = [];
            p0_1[0] = 0.5 * p0[0] + 0.5 * p1[0];
            p0_1[1] = 0.5 * p0[1] + 0.5 * p1[1];

            const p1_1 = [];
            p1_1[0] = 0.5 * p1[0] + 0.5 * p2[0];
            p1_1[1] = 0.5 * p1[1] + 0.5 * p2[1];

            const p0_2 = [];
            p0_2[0] = 0.5 * p0_1[0] + 0.5 * p1_1[0];
            p0_2[1] = 0.5 * p0_1[1] + 0.5 * p1_1[1];
            Bezie_line(p0, p0_1, p0_2);
            Bezie_line(p0_2, p1_1, p2);
        } else {
            Line(ctx, p0[0], p0[1], p2[0], p2[1]);
        }
    }

    const canvas = document.getElementById("hw_07");
    var ctx = canvas.getContext("2d");
    ctx.fillStyle = '#8B0000';
    canvas.setAttribute("tabindex", 0);
    let points = [];
    let counter = 0;

    function Mult_Mv(M, v) {
        const res = [];
        for (let i = 0; i < 4; ++i) {
            res.push(0);
            for (let j = 0; j < 4; ++j) {
                res[i] = res[i] + M[i * 4 + j] * v[j];
            }
        }
        return res;
    }

    canvas.addEventListener("click", function (event) {
        if (counter < 3) {
            points.push([event.offsetX, event.offsetY, 0]);
            ctx.fillRect(event.offsetX, event.offsetY, 2, 2);
            counter++;
        } else {
            alert("Press 'x', 'y' or 'z' key")
        }
    })
    let mode;
    canvas.addEventListener('keydown', function (e) {
        let out;
        mode = e.key
        if (!((mode === 'x') | (mode === 'y') || (mode === 'z'))) {
            mode = 'x'
        }
        const P0 = points[0];
        const P1 = points[1];
        const P2 = points[2];
        Bezie_line(P0, P1, P2);

        const P0_ = [];
        const P2_ = [];

        P0_[0] = P0[0];
        P0_[1] = P0[1];
        P0_[2] = P0[2];
        P2_[0] = P2[0];
        P2_[1] = P2[1];
        P2_[2] = P2[2];

        for (let i = 0; i < 360; i += 10) {

            const alpha = i * Math.PI / 180;
            P0_[0] -= P1[0];
            P0_[1] -= P1[1];
            P0_[2] -= P1[2];
            P2_[0] -= P1[0];
            P2_[1] -= P1[1];
            P2_[2] -= P1[2];

            const M_x = [1, 0, 0, 0,
                0, Math.cos(alpha), -1 * Math.sin(alpha), 0,
                0, Math.sin(alpha), Math.cos(alpha), 0,
                0, 0, 0, 1];

            const M_y = [Math.sin(alpha), 0, Math.cos(alpha), 0,
                0, 1, 0, 0,
                Math.cos(alpha), 0, -1 * Math.sin(alpha), 0,
                0, 0, 0, 1];

            const M_z = [Math.cos(alpha), -1 * Math.sin(alpha), 0, 0,
                Math.sin(alpha), Math.cos(alpha), 0, 0,
                0, 0, 0, 0,
                0, 0, 0, 1];

            if (mode === 'x') {
                out = Mult_Mv(M_x, [P0_[0], P0_[1], P0_[2], 1]);
                P0_[0] = out[0] + P1[0];
                P0_[1] = out[1] + P1[1];
                out = Mult_Mv(M_x, [P2_[0], P2_[1], P2_[2], 1]);
                P2_[0] = out[0] + P1[0];
                P2_[1] = out[1] + P1[1];
                Bezie_line(P0_, P1, P2_);

                P0_[0] = P0[0];
                P0_[1] = P0[1];
                P0_[2] = P0[2];
                P2_[0] = P2[0];
                P2_[1] = P2[1];
                P2_[2] = P2[2];

            } else if (mode === 'y') {
                out = Mult_Mv(M_y, [P0_[0], P0_[1], P0_[2], 1]);
                P0_[0] = out[0] + P1[0];
                P0_[1] = out[1] + P1[1];
                out = Mult_Mv(M_y, [P2_[0], P2_[1], P2_[2], 1]);
                P2_[0] = out[0] + P1[0];
                P2_[1] = out[1] + P1[1];
                Bezie_line(P0_, P1, P2_);

                P0_[0] = P0[0];
                P0_[1] = P0[1];
                P0_[2] = P0[2];
                P2_[0] = P2[0];
                P2_[1] = P2[1];
                P2_[2] = P2[2];

            } else if (mode === 'z') {
                out = Mult_Mv(M_z, [P0_[0], P0_[1], P0_[2], 1]);
                P0_[0] = out[0] + P1[0];
                P0_[1] = out[1] + P1[1];
                out = Mult_Mv(M_z, [P2_[0], P2_[1], P2_[2], 1]);
                P2_[0] = out[0] + P1[0];
                P2_[1] = out[1] + P1[1];
                Bezie_line(P0_, P1, P2_);

                P0_[0] = P0[0];
                P0_[1] = P0[1];
                P0_[2] = P0[2];
                P2_[0] = P2[0];
                P2_[1] = P2[1];
                P2_[2] = P2[2];

            }

        }

        counter = 0;
        points = [];
    })

</script>
</BODY>