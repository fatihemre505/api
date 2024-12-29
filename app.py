from flask import Flask, jsonify, request, abort
from flask_cors import CORS
import json

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# Sadece users.json dosyasını kullanacağız
def load_users():
    try:
        with open('users.json', 'r') as file:
            return json.load(file)['users']
    except FileNotFoundError:
        return []

def save_users(users):
    try:
        with open('users.json', 'w') as file:
            json.dump({"users": users}, file, indent=2)
    except Exception as e:
        abort(500, description=f"Kullanıcılar kaydedilirken hata oluştu: {str(e)}")

def get_user_by_id(user_id):
    user = next((u for u in users if u['id'] == user_id), None)
    if user is None:
        abort(404, description="Kullanıcı bulunamadı")
    return user

users = load_users()

@app.route('/users', methods=['GET'])
def get_users():
    return jsonify({"users": users})

@app.route('/users/<int:user_id>', methods=['GET'])
def get_user(user_id):
    user = get_user_by_id(user_id)
    return jsonify(user)

@app.route('/users', methods=['POST'])
def create_user():
    if not request.is_json:
        abort(400, description="JSON verisi gerekli")
    
    data = request.get_json()
    
    if not all(key in data for key in ['name', 'email', 'age']):
        abort(400, description="name, email ve age alanları gerekli")
    
    new_user = {
        'id': max([u['id'] for u in users], default=0) + 1,
        'name': data['name'],
        'email': data['email'],
        'age': data['age']
    }
    
    users.append(new_user)
    save_users(users)
    return jsonify(new_user), 201

if __name__ == '__main__':
    app.run(debug=True) 