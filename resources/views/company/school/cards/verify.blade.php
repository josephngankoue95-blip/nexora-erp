<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vérification de carte scolaire</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;background:#eaf0f7;font:15px Arial,sans-serif;color:#24334a;display:flex;align-items:center;justify-content:center;padding:20px}.box{width:100%;max-width:460px;background:white;border-radius:18px;overflow:hidden;box-shadow:0 16px 50px rgba(20,50,85,.12)}.top{background:#123a69;color:white;padding:27px;text-align:center;border-bottom:5px solid #e0b847}.top h1{margin:0;font-size:23px}.top p{margin:9px 0 0;font-size:13px;opacity:.9}.body{padding:30px;text-align:center}.check{display:inline-block;border-radius:50%;background:#e4f8ec;color:#137343;font-size:29px;width:58px;height:58px;line-height:58px}.status{font-size:19px;color:#137343;margin:15px 0 8px}.name{font-weight:bold;font-size:20px;color:#123a69;margin:16px 0 6px}.number{font-size:14px;color:#64748b}.note{font-size:12px;line-height:1.6;color:#6b7788;border-top:1px solid #e6ebf2;margin-top:24px;padding-top:17px}
</style>
</head>
<body>
<main class="box">
    <header class="top"><h1>Vérification scolaire</h1><p>Authentification d'une carte d'élève</p></header>
    <div class="body">
        <div class="check" aria-hidden="true">✓</div>
        <h2 class="status">Carte scolaire valide</h2>
        <p>Une inscription active correspond à cette carte.</p>
        <div class="name">{{ $student->last_name }} {{ $student->first_name }}</div>
        <div class="number">Matricule : {{ $student->student_number }}</div>
        <div class="note">Cette vérification confirme l'existence de l'inscription à la date de consultation. Le lien de vérification peut expirer.</div>
    </div>
</main>
</body>
</html>
