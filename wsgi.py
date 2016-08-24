import json
import os
from webob import Request, Response
from webob.exc import HTTPFound as Redirect
from settings import vardir
from tempita import Template

def application(environ, start_response):
    req = Request(environ)
    if req.path_info.strip("/") and not req.path_info.endswith("/"):
        return Response("add a trailing slash")(environ, start_response)
    
    path = req.path_info.strip("/")
    if path == "":
        ret = index(req)
    elif path == "pages":
        ret = pages(req)
    elif path == "signs":
        ret = signs(req)
    elif path == "images":
        ret = images(req)
    elif path == "themes":
        ret = themes(req)
    elif path.startswith("themes/"):
        ret = themes_theme(req)
    elif path == "fonts":
        ret = fonts(req)
    else:
        ret = path
    if not isinstance(ret, Response):
        ret = Response(ret)
    return ret(environ, start_response)

def index(req):
    return """
<ul>
<li><a href="./pages/">Edit pages config</a></li>
<li><a href="./signs/">Edit signs config</a></li>
<li><a href="./themes/">Upload theme files</a></li>
<li><a href="./images/">Upload image files</a></li>
<li><a href="./fonts/">Upload font files</a></li>
</ul>
"""

json_tmpl = Template("""
<form method="POST">
{{if error}}
<div style="border: 1px dotted red">{{error}}</div>
{{endif}}
<textarea cols="100" rows="100" name="json">{{ jsoned }}</textarea>
<input type="submit">
</form>
""")

upload_tmpl = Template("""
<form enctype="multipart/form-data" method="POST">
  <label>Filename: <input name="filename"></label> 
  <input type="file" name="file">
  <input type="submit">
</form>
<ul>
{{for file in files}}
<li>{{ file }}</li>
{{endfor}}
</ul>
""")

def pages(req):
    if req.method == "GET":
        with open(os.path.join(vardir, "pages.json")) as fp:
            jsoned = json.dumps(json.loads(fp.read()), indent=2)
        return json_tmpl.substitute({"jsoned": jsoned, "error": None})

    jsoned = req.POST['json']
    try:
        data = json.loads(jsoned)
    except Exception, e:
        error = str(e)
        return json_tmpl.substitute({"jsoned": jsoned, "error": error})
    with open(os.path.join(vardir, "pages.json"), 'w') as fp:
        fp.write(jsoned)
    return Redirect(location=".")

def signs(req):
    if req.method == "GET":
        with open(os.path.join(vardir, "images.json")) as fp:
            jsoned = json.dumps(json.loads(fp.read()), indent=2)
        return json_tmpl.substitute({"jsoned": jsoned, "error": None})

    jsoned = req.POST['json']
    try:
        data = json.loads(jsoned)
    except Exception, e:
        error = str(e)
        return json_tmpl.substitute({"jsoned": jsoned, "error": error})
    with open(os.path.join(vardir, "images.json"), 'w') as fp:
        fp.write(jsoned)
    return Redirect(location=".")

def images(req):
    if req.method == "GET":
        return upload_tmpl.substitute({
            "files": os.listdir(os.path.join(vardir, "images"))})
    filename = req.POST['filename']
    if '/' in filename:
        return Redirect(location='.')
    with open(os.path.join(vardir, "images", filename), 'w') as fp:
        fp.write(req.POST['file'].file.read())
        return Redirect(location='.')

def themes_theme(req):
    theme = req.path_info.strip("/").split("/")[-1]
    if req.method == "GET":
        with open(os.path.join(vardir, "templates", "themes", theme)) as fp:
            return json_tmpl.substitute({"jsoned": fp.read(), "error": None})

def themes(req):
    if req.method == "GET":
        return upload_tmpl.substitute({
            "files": os.listdir(os.path.join(vardir, "templates", "themes"))})
    filename = req.POST['filename']
    if '/' in filename:
        return Redirect(location='.')
    with open(os.path.join(vardir, "templates", "themes", filename), 'w') as fp:
        fp.write(req.POST['file'].file.read())
        return Redirect(location='.')

def fonts(req):
    if req.method == "GET":
        return upload_tmpl.substitute({
            "files": os.listdir(os.path.join(vardir, "fonts"))})
    filename = req.POST['filename']
    if '/' in filename:
        return Redirect(location='.')
    with open(os.path.join(vardir, "images", filename), 'w') as fp:
        fp.write(req.POST['file'].file.read())
        return Redirect(location='.')
