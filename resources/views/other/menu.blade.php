<nav class="main-nav">
    <ul>
        <li><a href="reports.cursor.style">📊 Reports</a></li>
        <li><a href="/admin/categories/create">📁 Categories</a></li>
        <li><a href="/admin/cursors/create">🖱️ Cursors</a></li>
    </ul>
</nav>

<style>
.main-nav {
    background-color: #1f2937;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.main-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 20px;
}

.main-nav li {
    display: inline-block;
}

.main-nav a {
    color: #ffffff;
    text-decoration: none;
    padding: 8px 16px;
    font-weight: 500;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.main-nav a:hover {
    background-color: #374151;
}

.main-nav a.active {
    background-color: #4f46e5;
}

</style>