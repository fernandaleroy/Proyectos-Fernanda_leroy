# 📊 EDA – Funciones y herramientas principales (pandas + matplotlib + seaborn)

Este resumen concentra **las funciones más usadas en Análisis Exploratorio de Datos (EDA)**: inspección, estadísticos, limpieza y visualización. 
Se recomienda ver la documentación de las funciones para mayor comprensión

---

## 🔎 Inspección inicial de datos (pandas)
```python
import pandas as pd

df = pd.read_csv("data.csv")

df.shape              # tamaño (filas, columnas)
df.head()             # primeras filas
df.tail()             # últimas filas
df.sample(5)          # muestra aleatoria
df.info()             # tipos, nulos, memoria
df.describe()         # estadísticos numéricos
df.describe(include='object')   # para categóricas
df.nunique()          # número de valores únicos por columna
```

---

## 🧹 Limpieza básica
```python
df.isna().sum()             # nulos por columna
df.dropna()                 # eliminar nulos
df.fillna(0)                # rellenar nulos con 0
df.duplicated().sum()       # filas duplicadas
df.drop_duplicates()        # eliminar duplicados

df['col'] = pd.to_numeric(df['col'], errors='coerce')    # convertir a numérico
df['fecha'] = pd.to_datetime(df['fecha'], errors='coerce')
```

---

## 🚨 Outliers

```python
# Identificar outliers usando el método IQR
Q1 = df['col'].quantile(0.25)
Q3 = df['col'].quantile(0.75)
IQR = Q3 - Q1
outliers = df[(df['col'] < Q1 - 1.5 * IQR) | (df['col'] > Q3 + 1.5 * IQR)]

# Visualizar outliers con boxplot
import matplotlib.pyplot as plt
plt.boxplot(df['col'].dropna())
plt.title("Boxplot para detectar outliers")
plt.show()

# Visualizar con Matplotlib

plt.scatter(df.index, df['col'])
plt.xlabel("Índice")
plt.ylabel("Valor de 'col'")
plt.title("Scatterplot para detectar outliers")
plt.show()

# Scatterplot Seaborn

import seaborn as sns

sns.scatterplot(x=df.index, y=df['col'])
plt.title("Scatterplot Seaborn para detectar outliers")
plt.xlabel("Índice")
plt.ylabel("Valor de 'col'")
plt.show()
```

---

## � Análisis de distribuciones

```python
# Análisis de normalidad
from scipy import stats

# Test de Shapiro-Wilk para normalidad (muestras pequeñas)
stat, p_value = stats.shapiro(df['col'].dropna())
print(f"Shapiro-Wilk: estadístico={stat:.4f}, p-valor={p_value:.4f}")

# QQ-plot para verificar normalidad visualmente
stats.probplot(df['col'].dropna(), dist="norm", plot=plt)
plt.title("QQ-plot para verificar normalidad")
plt.show()

# Análisis de asimetría y curtosis
print(f"Asimetría (skewness): {df['col'].skew():.3f}")  # 0=simétrico, >0=cola derecha, <0=cola izquierda
print(f"Curtosis: {df['col'].kurtosis():.3f}")  # 0=normal, >0=puntiaguda, <0=plana

# Transformaciones para normalizar
df['col_log'] = np.log1p(df['col'])  # log(1+x) para evitar log(0)
df['col_sqrt'] = np.sqrt(df['col'])  # raíz cuadrada
```

---

## �📈 Estadísticas y agrupaciones
```python
df['col'].value_counts()                  # frecuencia absoluta
df['col'].value_counts(normalize=True)    # frecuencia relativa (%)

df.groupby('grupo')['valor'].mean()       # promedio por grupo
df.groupby(['g1','g2']).agg({'y':['mean','median','count']})

pd.crosstab(df['sexo'], df['sobrevive'])        # tabla de contingencia
pd.pivot_table(df, index='g1', columns='g2', values='y', aggfunc='mean')
```

---

## 📊 Visualización con Matplotlib
```python
import matplotlib.pyplot as plt

# Histograma
plt.hist(df['edad'], bins=20)
plt.xlabel("Edad"); plt.ylabel("Frecuencia")

# Boxplot
plt.boxplot(df['edad'].dropna())

# Barras categóricas
df['sexo'].value_counts().plot(kind='bar')

# Dispersión
plt.scatter(df['edad'], df['ingreso'])
```

---

## 🌈 Visualización con Seaborn
```python
import seaborn as sns

sns.histplot(df['edad'], kde=True)                         # histograma + densidad
sns.boxplot(x='sexo', y='edad', data=df)                   # boxplot categórico
sns.countplot(x='pclass', hue='sex', data=df)              # barras apiladas
sns.scatterplot(x='edad', y='fare', hue='survived', data=df)
sns.heatmap(df.corr(), annot=True, cmap="coolwarm")        # mapa de calor correlaciones
```

---

## 📐 Funciones matemáticas

```python
df['col'].mean()        # media aritmética
df['col'].median()      # mediana
df['col'].mode()        # moda
df['col'].std()         # desviación estándar
df['col'].var()         # varianza
df['col'].min()         # valor mínimo
df['col'].max()         # valor máximo
df['col'].sum()         # suma total
df['col'].quantile(0.25)  # percentil 25
df['col'].quantile(0.75)  # percentil 75

```
> Se puede aplicar directamente estas funciones al DataFrame (por ejemplo, `df.mean()`, `df.median()`, etc.) y devolverán la medida para cada columna numérica.


Estas funciones permiten calcular **estadísticos descriptivos** básicos para analizar la distribución y tendencia de los datos numéricos.

---

## 📅 Series temporales básicas

```python
# Conversión y manipulación de fechas
df['fecha'] = pd.to_datetime(df['fecha'])
df['año'] = df['fecha'].dt.year
df['mes'] = df['fecha'].dt.month
df['dia_semana'] = df['fecha'].dt.day_name()

# Análisis temporal
df.set_index('fecha').resample('M')['valor'].mean().plot()  # promedio mensual
plt.title('Tendencia mensual')

# Estacionalidad simple
df.groupby(df['fecha'].dt.month)['valor'].mean().plot(kind='bar')
plt.title('Patrón estacional por mes')
```

---

