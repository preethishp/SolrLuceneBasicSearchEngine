import networkx as nx

G = nx.read_edgelist("/home/preethishp/Documents/edgeList.txt", create_using=nx.DiGraph())
#G = nx.read_edgelist("/home/preethishp/Documents/edgeList.txt")
#pr = nx.pagerank(G,0.85,None,30,1e-06,None,'weight',None)
pr = nx.pagerank(G,alpha=0.85,personalization=None,max_iter=30,tol=1e-06,nstart=None,weight='weight',dangling=None)
print(len(pr))

with open("/home/preethishp/external_pageRankFile.txt","w") as filehandle:
    for key, value in pr.items():
        filehandle.write(key+"="+str(value)+"\n")

